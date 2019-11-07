<?php declare(strict_types=1);

namespace App\Service;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class ServiceContext
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class MessageRedis
{
    private $redis;

    function __construct($redis = '')
    {
        $this->redis = $this->redis ?? $redis;
    }

    /**
     * 查询超时消息
     * @param $key
     * @param $inf
     * @param $time
     * @return bool
     */
    public function getMessageOvertime($key, $inf, $time): array
    {
        return $this->redis->zRangeByScore($key, $inf, (string)$time);
    }

    /**
     * 查询消息
     * @param $key
     * @param $msgId
     * @return mixed
     */
    public function getMessageData($key, $msgId)
    {
        return $this->redis->hget($key, (string)$msgId);
    }

    /**
     * 查询主动方消息状态
     */
    public function getMasterMessage($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 存储消息数据到可视化UI界面
     */
    public function addMessageData($key, $msgId, $data)
    {
        return $this->redis->hset($key, $msgId, $data);
    }

}