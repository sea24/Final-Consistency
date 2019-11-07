<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/12
 * Time: 21:38
 */

namespace App\Fallback;


use Swoft\Rpc\Client\Annotation\Mapping\Fallback;


/**
 * Class Test
 * @package App\Fallback
 * @Fallback(name="Test",version="1.0")
 */
class Test
{
    public function confirmMsgToSend(): array
    {
        return ["降级处理:当前降级方法" . 'confirmMsgToSend'];
    }

}