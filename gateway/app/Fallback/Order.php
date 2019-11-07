<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/12
 * Time: 21:38
 */

namespace App\Fallback;


use MeiQuick\Rpc\Lib\Order as LibOrder;
use Swoft\Rpc\Client\Annotation\Mapping\Fallback;


/**
 * Class DeleteFallback
 * @package App\Fallback
 * @Fallback(name="order",version="1.0")
 */
class Order
{
    public function order(): array
    {
        return ["降级处理:当前降级方法" . 'order'];
    }

}