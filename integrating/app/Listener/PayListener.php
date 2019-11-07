<?php declare(strict_types=1);
/**
 * server启动事件
 */

namespace App\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Exception\SwoftException;
use Swoft\Log\Helper\CLog;
use Swoft\Redis\Redis;
use Swoft\Server\ServerEvent;
use Swoft\SwoftEvent;
use Swoft\Redis\Pool;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use App\Service\RpcClinetContext;
use MeiQuick\Rpc\Lib\Message;

/**
 *
 * @since 2.0
 * @Listener(ServerEvent::BEFORE_START)
 */
class PayListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws SwoftException
     */

    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $connectionRedis;

    /**
     * @Reference(pool="message.pool", fallback="MessageFallback")
     *
     * @var Message
     */
    private $messageService;

    public function handle(EventInterface $event): void
    {
        /*$callBack = function () {
            go(function () {

                //开启上下文
                $context = RpcClinetContext::new();
                \Swoft\Context\Context::set($context);

                //消费任务
                $exchangeName = 'order';
                $routeKey = '/order';
                $queueName = 'order';
                $connection = $this->rabbit->connect();
                vdump($this->rabbit);
                $connectionRabbit = $connection->connection;
                $channel = $connectionRabbit->channel();

                $channel->queue_declare($queueName, false, true, false, false);
                $channel->exchange_declare($exchangeName, \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT, false, true, false);

                //队列绑定交换机跟路由
                $channel->queue_bind($queueName, $exchangeName, $routeKey);

                $channel->basic_consume($queueName, '', false, false, false, false, function ($message) {

                    $data = json_decode($message->body, true);
                    //1.记录消息任务是否完成（消费幂等:同一个任务执行10次跟执行一次的效果是一样）
                    $statusJob = $this->connectionRedis->get("integrating_message_job", (string)$data['msg_id']);
                    if ($statusJob == 2) { //已经消费成功了
                        $this->messageService->ackMsg($data['msg_id']); //这个任务已经消费成功了
                    } elseif ($statusJob == 1) { //任务正在执行
                        var_dump("任务正在执行当中");
                        return;
                    } else {
                        //执行任务当中,并且设置释放的时间
                        $this->connectionRedis->setex("integrating_message_job:" . $data['msg_id'], 10, 1);
                        //完成则处理，如果业务异常消息系统重新推送

                        $this->connectionRedis->set("integrating_message_job:" . $data['msg_id'], 2);//执行任务完毕
                        $this->messageService->ackMsg($data['msg_id']); //这个任务已经消费成功了
                        //回应ack
                        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                    }
                });

                while ($channel->is_consuming()) {
                    $channel->wait(); //阻塞消费
                }

            });
        };

        //防止进程意外崩溃，回收子进程
        \Swoole\Process::signal(SIGCHLD, function ($sig) use ($callBack) {
            while ($ret = \Swoole\Process::wait(false)) {
                $p = new  \Swoole\Process($callBack);
                $p->start();
            }
        });

        //创建单独进程处理任务
        $p = new  \Swoole\Process($callBack);
        $p->start();*/
    }
}