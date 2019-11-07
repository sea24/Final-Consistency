<?php

namespace App\Fallback;

use Swoft\Rpc\Client\Annotation\Mapping\Fallback;
use MeiQuick\Rpc\Lib\Message;


/**
 * Class MessageServiceFallback
 * @package App\Fallback
 * @Fallback(name="MessageFallback",version="1.0")
 */
class MessageServiceFallback implements Message
{
    /**
     * 预发送消息
     * @return array
     */
    public function prepareMsg($prepareMsgData): array
    {
        return ['预发送消息降级'];
    }

    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmMsgToSend($msgId,$flag): array
    {

        return ['确认并且投递降级'];

    }

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg(): array
    {
        return ['任务消费降级'];
    }

    /**
     * 消息状态确认
     * @return array
     */
    public function SelectMsgTime($msgType): array
    {
        return ['查询任务状态降级'];
    }

}