<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 21:59
 */

namespace App\Listener;

use MeiQuick\Rpc\Lib\Message;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Redis\Pool;
use Swoft\Server\ServerEvent;
use Swoft\Server\Swoole\SwooleEvent;
use App\Service\MessageRedis;
use App\Service\RpcClinetContext;
use Swoft\Context\Context;

/**
 * Class RegisterServer
 * @package App\Listener
 * @Listener(SwooleEvent::START)
 */
class MessageStatusListener implements EventHandlerInterface
{
    /**
     * @Reference(pool="message.pool",fallback="MessageFallback")
     *
     * @var Message
     */
    private $messageService;

    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    public $redis;

    public function handle(EventInterface $event): void
    {
        $event = bean('config')->get('messageconfig.event');

        //当前定时时间，必须大于服务发现时间
        swoole_timer_tick($event['timer_tick'], function () use ($event) {
            sgo(function () use ($event) {
                try {
                    //Redis Key
                    $redisKey = bean('config')->get('messageconfig.redis_key');
                    //消息配置
                    $messageConfig = bean('config')->get('messageconfig.message_status');
                    //主动方配置
                    $masterConfig = bean('config')->get('messageconfig.master_config');
                    //设置上下文
                    $context = RpcClinetContext::new();
                    \Swoft\Context\Context::set($context);
                    //查询超时消息
                    $messageRedis = new MessageRedis($this->redis);
                    $service = $messageRedis->getMessageOvertime($redisKey['message_system_time'], "-inf", time() - $event['message_out_time']);

                    if (!$service) {
                        vdump('未检测到超时消息');
                        return '未检测到超时消息';
                    }

                    //处理超时消息
                    foreach ($service as $k => $v) {
                        $data = json_decode($messageRedis->getMessageData($redisKey['message_system'], $v));

                        switch ($data->status) {

                            //消息状态子系统 (已经进入消息子系统但是未投递的）
                            case $messageConfig['status_undelivered']:

                                //判断主动方是否已经消费完成
                                $messageRedisResult = $messageRedis->getMasterMessage($masterConfig['master_message_job'] . (string)$v);
                                if ($messageRedisResult) {

                                    //主动方已消费，重新投递
                                    $this->messageService->confirmMsgToSend($v, $messageConfig['status_undelivered']);
                                } else {
                                    //删除消息
                                    $this->ackMsg($v);
                                }
                                break;

                            //已投递，超时没有被正确消费（消息恢复系统）
                            case $messageConfig['status_delivered']:

                                //是否超过最大投递次数
                                if ($data->message_retries_number >= $messageConfig['message_retries_number']) {

                                    //写入ui可视化手动投递界面
                                    $messageRedis->addMessageData($redisKey['message_ui_data'], $v, json_encode($data));
                                    vdump('超过最大投递次数');

                                    //删除消息
                                    $this->ackMsg($v);
                                } else {
                                    //主动方已消费，重新投递
                                    vdump('主动方已消费，重新投递');
                                    $this->messageService->confirmMsgToSend($v, $messageConfig['status_delivered']);
                                }
                                break;

                            default :
                                return '消息状态异常';
                        }
                    }
                } catch (\Exception $e) {
                    vdump($e->getMessage());
                }
            });

        });
    }

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg($msgId): array
    {
        //Redis Key
        $redisKey = bean('config')->get('messageconfig.redis_key');
        //删除已确认消费的消息
        $result = $this->redis->transaction(function (\Redis $redis) use ($msgId, $redisKey) {
            $redis->hdel($redisKey['message_system'], (string)$msgId);
            $redis->zrem($redisKey['message_system_time'], (string)$msgId);
        });
        if ($result[0] !== false) {
            $data = ['status' => 1, 'result' => '任务消费成功'];
        } else {
            $data = ['status' => 0, 'result' => '任务消费失败'];
        }
        return $data;
    }
}