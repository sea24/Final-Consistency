<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Rpc\Service;

use MeiQuick\Rpc\Lib\Order as LibRpc;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use Swoft\Redis\Pool;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class UserService
 *
 * @since 2.0
 *
 * @Service()
 */
class Order implements LibRpc
{
    /**
     * @Inject("messageRedis.pool")
     * @var Pool
     */
    private $redis;

    /**
     * @param $data
     * @return array
     */
    public function order($data): array
    {
        //执行主业务

        //业务执行成功
        $this->redis->setex("master_message_job:" . (string)$data['msg_id'], 86400, (string)1);
        //调用mysql更新信息（业务逻辑）

        return ['status' => 1, 0 => 'my name is order' . mt_rand(1, 9999)];
    }
}
