<?php declare(strict_types=1);


namespace App\Rpc\Service;

use MeiQuick\Rpc\Lib\Message;
use App\Rpc\Lib\UserInterface;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Co;
use Swoft\Redis\Pool;
use Swoft\Redis\Redis;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use App\Service\MessageRedis;

/**
 * Class UserService
 *
 * @since 2.0
 *
 * @Service()
 */
class MessageService implements Message
{
    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $connectionRedis;

    /**
     * @Inject("rabbit.pool")
     * @var \Six\Rabbit\Pool
     */
    private $rabbit;

    /**
     * 预发送消息
     * @return array
     */
    public function prepareMsg($prepareMsgData): array
    {
        $msgId = $prepareMsgData['msg_id'];
        //存数据，还要存时间，依据时间查找超时的任务
        $redisKey = bean('config')->get('messageconfig.redis_key');
        $result = $this->connectionRedis->transaction(function (\Redis $redis) use ($msgId, $prepareMsgData, $redisKey) {
            $redis->hset($redisKey['message_system'], (string)$msgId, json_encode($prepareMsgData));
            $redis->zAdd($redisKey['message_system_time'], $prepareMsgData['create_time'], (string)$msgId);
        });
        if ($result[0] == false) {
            return ['status' => 0, 'result' => 'yu fa song no'];
        }
        return ['status' => 1, 'result' => 'yu fa song yes'];
    }

    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmMsgToSend($msgId, $flag): array
    {
        try {
            //Redis Key
            $redisKey = bean('config')->get('messageconfig.redis_key');

            //获取消息路由并投递
            $messageRedis = new MessageRedis($this->connectionRedis);
            $data = json_decode($messageRedis->getMessageData($redisKey['message_system'], $msgId), true);

            $connection = $this->rabbit->connect();
            $connectionRabbit = $connection->connection;
            $exchangeName = $data['routing']['exchange_name'];
            $routeKey = $data['routing']['route_key'];
            $queueName = $data['routing']['queue_name'];
            $channel = $connectionRabbit->channel();

            /**
             * 创建队列(Queue)
             * name: hello         // 队列名称
             * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
             * durable: true       // 是否持久化，设置false是存放到内存中的，RabbitMQ重启后会丢失
             * exclusive: false    // 是否排他，指定该选项为true则队列只对当前连接有效，连接断开后自动删除
             *  auto_delete: false // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
             * cun'chu */
            $channel->queue_declare($queueName, false, true, false, false);

            /**
             * 创建交换机(Exchange)
             * name: vckai_exchange// 交换机名称
             * type: direct        // 交换机类型，分别为direct/fanout/topic，参考另外文章的Exchange Type说明。
             * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
             * durable: false      // 是否持久化，设置false是存放到内存中的，RabbitMQ重启后会丢失
             * auto_delete: false  // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
             */
            $channel->exchange_declare($exchangeName, \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT, false, true, false);

            // 绑定消息交换机和队列
            $channel->queue_bind($queueName, $exchangeName);
            if (!empty($data)) {

                $data['status'] = 2;
                if ($flag == 2) {
                    //被消息恢复子系统投递的任务
                    $data['message_retries_number'] = $data['message_retries_number'] + 1;
                }
                $data = json_encode($data);
                /**
                 * 创建AMQP消息类型
                 * delivery_mode 消息是否持久化
                 * AMQPMessage::DELIVERY_MODE_NON_PERSISTENT  不持久化
                 * AMQPMessage::DELIVERY_MODE_PERSISTENT      持久化
                 */

                $msg = new \PhpAmqpLib\Message\AMQPMessage($data, ['delivery_mode' => \PhpAmqpLib\Message\AMQPMessage:: DELIVERY_MODE_NON_PERSISTENT]);
                //发布消息到交换机当中,并且绑定好路由关系
                if ($this->connectionRedis->hset("message_system", (string)$msgId, $data) == 0 && $channel->basic_publish($msg, $exchangeName, $routeKey) == null) {
                    //将消息投递给MQ(实时消息服务)
                    $data = ['status' => 1, 'result' => 'tou di yes'];
                } else {
                    $data = ['status' => 0, 'result' => 'tou di no one'];
                }
            } else {
                $data = ['status' => 0, 'result' => 'tou di no two'];
            }
            $channel->close();
            $connection->release(true);
            return $data;
        } catch (\Exception $e) {
            var_dump($e->getFile(), $e->getLine(), $e->getMessage());
        }
        return $data;

    }

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg($msgId = ''): array
    {
        return ['status' => 1, 'result' => '任务消费成功'];
    }

    /**
     * 消息状态确认
     * @return array
     */
    public function SelectMsgTime($msgType): array
    {
        return ['status' => 1, 'result' => '查询到的任务状态'];
    }


}