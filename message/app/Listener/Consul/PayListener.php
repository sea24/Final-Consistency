<?php declare(strict_types=1);
/**
 * server启动事件
 */

namespace App\Listener\Consul;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Exception\SwoftException;
use Swoft\Log\Helper\CLog;
use Swoft\Redis\Redis;
use Swoft\Server\ServerEvent;

/**
 *
 * @since 2.0
 *
 * @Listener(ServerEvent::BEFORE_START)
 */
class PayListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws SwoftException
     */
    public function handle(EventInterface $event): void
    {
        $config = bean('config')->get('pay.consul');
        bean('consulProvider')->registerServer($config);
    }
}
