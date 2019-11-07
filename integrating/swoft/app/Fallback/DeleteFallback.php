<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/12
 * Time: 21:38
 */

namespace App\Fallback;


use MeiQuick\Rpc\Lib\GetList;
use Swoft\Rpc\Client\Annotation\Mapping\Fallback;


/**
 * Class DeleteFallback
 * @package App\Fallback
 * @Fallback(name="testdelete",version="1.0")
 */
class DeleteFallback
{
    public function sendBigContent(): array
    {
        return ["降级处理:当前降级方法sendBigContent"];
    }
    
}