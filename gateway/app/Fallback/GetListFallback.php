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
 * Class GetListFallback
 * @package App\Fallback
 * @Fallback(name="getList",version="1.0")
 */
class GetListFallback
{
    public function getList(): array
    {
        return ["降级处理:当前降级方法".'updateOrder'  ];
    }

    public function delete()
    {
        return ['test'];
    }
}