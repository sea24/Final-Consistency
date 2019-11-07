<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;

/**
 * 运算时间测试类
 *
 * @Controller("Test")
 */
class TestController
{
    /**
     * 闭包递归 计算阶乘
     *
     * @RequestMapping("test/{number}")
     *
     * @param int $number
     *
     * @return array
     */
    public function factorial(int $number): array
    {
        $factorial = function ($arg) use (&$factorial) {
            if ($arg == 1) {
                return $arg;
            }

            return $arg * $factorial($arg - 1);
        };

        return [$factorial($number)];
    }

    /**
     * 计算1～1000的和，最后休眠1s
     *
     * @RequestMapping()
     */
    public function sumAndSleep(): array
    {
        $sum = 0;
        for ($i = 1; $i <= 1000; $i++) {
            $sum = $sum + $i;
        }

        usleep(1000);
        return [$sum];
    }

    /**
     * @RequestMapping(route="aop",method="GET")
     * @param $get
     */
    public function test(Request $request)
    {
        //导入ES
         return 779789;
    }
}