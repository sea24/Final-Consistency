<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller\Rpc;

use MeiQuick\Rpc\Lib\Message;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Redis\Redis;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use MeiQuick\Rpc\Lib\Order;

/**
 * Class OrderController
 *
 * @since 2.0
 *
 * @Controller("order")
 */
class OrderController
{

    /**
     * @Reference(pool="order.pool", fallback="order")
     *
     * @var Order
     */
    private $order;

    /**
     * @Reference(pool="message.pool", fallback="MessageFallback")
     *
     * @var Message
     */
    private $message;

    /**
     * @RequestMapping("updateOrder")
     *
     * @return array
     */
    public function updateOrder(): array
    {
        //预发送消息（消息状态子系统）
        $msgId = session_create_id(md5(uniqid()));
        $prepareMsgData = [
            'msg_id' => $msgId,
            'version' => 1,
            'create_time' => time(),
            'message_body' => ['order_id' => 12133, 'shop_id' => 2],
            'consumer_queue' => 'order', //消费队列（消费者）
            'message_retries_number' => 0, //重试次数，
            'status' => 1, //消息状态
            'routing' => [
                'exchange_name' => 'order', //交换机
                'route_key' => '/order', //路由
                'queue_name' => 'order', //队列名称
            ],
        ];

        //获取分布式系统的mysql.log 并记录（）kafka
        //预存储消息
        $result = $this->message->prepareMsg($prepareMsgData);
        $data = [
            'order_id' => 1,
            'msg_id' => $msgId
        ];
        if ($result['status']) {
            $orderResult = $this->order->order($data);
            if ($result['status']) {
                $confirmMsgToSend = $this->message->confirmMsgToSend($msgId, 1);//更新订单
            }
        }
        "update user set 'name'='123' where id=5";
        return [$result, $orderResult, $confirmMsgToSend];
    }
}

