<?php declare(strict_types=1);


namespace App\Service;


use Six\Rabbit\Connection;
use Swoft\Bean\Annotation\Mapping\Bean;


use MeiQuick\Rpc\Lib\Message;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Redis\Pool;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use Swoft\Server\ServerEvent;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class AmqpConsumption
 * @since 1.0
 * @Bean()
 */
class AmqpConsumption
{

    private $rabbit;

    private $connectionRedis;

    private $exchangeName;

    private $routeKey;

    private $queueName;

    private $class;

    private $match;


    public function handle($exchangeNam, $routeKey, $queueName, $class, $match, $pool)
    {
        try {
            if (!class_exists($class)) {
                return sprintf('当前类:%s不存在', $class);
            }

            if (!method_exists($class, $match)) {
                return sprintf('当前方法:%s不存在', $match);
            }

            //连接池
            $this->rabbit = $pool['rabbit'];
            $this->connectionRedis = $pool['connectionRedis'];

            $this->exchangeName = $exchangeNam;
            $this->routeKey = $routeKey;
            $this->queueName = $queueName;
            $this->class = $class;
            $this->match = $match;

            $callBack = function () use ($exchangeNam, $routeKey, $queueName, $class, $match) {
                go(function () use ($exchangeNam, $routeKey, $queueName, $class, $match) {
                    //消费任务
                    $connection = $this->rabbit->connect();
                    $connectionRabbit = $connection->connection;
                    $channel = $connectionRabbit->channel();

                    $channel->queue_declare($queueName, false, true, false, false);
                    $channel->exchange_declare($this->exchangeName, \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT, false, true, false);

                    //队列绑定交换机跟路由
                    $channel->queue_bind($queueName, $this->exchangeName, $routeKey);

                    $channel->basic_consume($queueName, '', false, false, false, false, function ($message) {

                        $data = json_decode($message->body, true);

                        //幂等性
                        $statusJob = $this->connectionRedis->get("integrating_message_job", (string)$data['msg_id']);

                        //已经消费成功了
                        if ($statusJob == 2) {

                            //通知消息系统，已经处理处理完成，删除掉已消费消息id
                            vdump("已经消费成功了");

                        } elseif ($statusJob == 1) { //任务正在执行

                            vdump("任务正在执行当中");
                            return false;

                        } else { //执行业务操作

                            //执行任务当中,并且设置释放的时间
                            $this->connectionRedis->setex("integrating_message_job:" . $data['msg_id'], 10, 1);

                            //更新业务，完成则处理，如果业务异常消息系统重新推送
                            $class = new $this->class;
                            $match = $this->match;
                            if ($class->$match($data)) {

                                //执行任务完毕，延迟删除
                                $this->connectionRedis->setex("integrating_message_job:" . $data['msg_id'], 86400, (string)2);

                                //确认消息
                                $this->ackMsg($data['msg_id']);

                                //响应ack
                                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                            }
                        }
                    });

                    while ($channel->is_consuming()) {
                        $channel->wait();
                    }
                });
            };

            \Swoole\Process::signal(SIGCHLD, function ($sig) use ($callBack) {
                while ($ret = \Swoole\Process::wait(false)) {
                    $p = new  \Swoole\Process($callBack);
                    $p->start();
                }
            });

            $p = new  \Swoole\Process($callBack);
            $p->start();

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg($msgId): array
    {
        //删除已确认消费的消息
        $result = $this->connectionRedis->transaction(function (\Redis $redis) use ($msgId) {
            $redis->hdel('message_system', (string)$msgId);
            $redis->zrem('message_system_time', (string)$msgId);
        });
        if ($result[0] !== false) {
            $data = ['status' => 1, 'result' => '任务消费成功'];
        } else {
            $data = ['status' => 0, 'result' => '任务消费失败'];
        }
        return $data;
    }
}