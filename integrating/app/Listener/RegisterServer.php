<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 21:59
 */

namespace App\Listener;

use MeiQuick\Rpc\Lib\Message;
use Swoft\Redis\Redis;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Redis\Pool;
use Swoft\Server\ServerEvent;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use App\Service\RpcClinetContext;
use App\Service\AmqpConsumption;

/**
 * Class RegisterServer
 * @package App\Listener
 * @Listener(ServerEvent::BEFORE_START)
 */
class RegisterServer implements EventHandlerInterface
{
    /**
     * @Inject("rabbit.pool")
     * @var \Six\Rabbit\Pool
     */
    private $rabbit;

    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $connectionRedis;

    public function handle(EventInterface $event): void
    {
        //注册服务
        $config = bean('config')->get('provider.consul');
        bean('consulProvider')->registerServer($config);

        //业务消息处理
        $exchangeName = 'order';
        $routeKey = '/order';
        $queueName = 'order';
        //连接池
        $pool = [
            'rabbit' => $this->rabbit,
            'connectionRedis' => $this->connectionRedis,
        ];
        $amqpConsumption = new AmqpConsumption();
        $amqpConsumption->handle($exchangeName, $routeKey, $queueName, "App\Service\TestService", "test", $pool);
    }
}