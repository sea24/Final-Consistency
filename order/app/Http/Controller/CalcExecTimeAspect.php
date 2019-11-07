<?php
/**
 * Created by PhpStorm.
 * User: yanghailong
 * Date: 2019/9/24
 * Time: 10:48 AM
 */

namespace App\Http\Controller;

use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\AfterReturning;
use Swoft\Aop\Annotation\Mapping\AfterThrowing;
use Swoft\Aop\Annotation\Mapping\Around;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Aop\Point\JoinPoint;
use Swoft\Aop\Point\ProceedingJoinPoint;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoole\Process;

/**
 * AOP切面类
 *
 * @since 2.0
 *
 * 声明切面类
 * @Aspect(order=1)
 *
 * 声明为 PointBean 类型的切面
 * @PointBean(include={"App\Http\Controller\TestController"})
 */
class CalcExecTimeAspect
{
    protected $start;

    /**
     * 前置通知
     * @Before()
     */
    public function before()
    {
        $this->start = microtime(true);
    }

    /**
     * 后置通知
     * @After()
     */
    public function after(JoinPoint $joinPoint)
    {
        $method = $joinPoint->getMethod();
        $after = microtime(true);
        $runtime = ($after - $this->start) * 1000;
        echo "{$method} 方法，本次执行时间为: {$runtime}ms\n";
    }

    /**
     * 最终返回通知
     * @AfterReturning()
     *
     * @param JoinPoint $joinPoint
     *
     * @return mixed
     */
    public function afterReturn(JoinPoint $joinPoint)
    {
        $ret = $joinPoint->getReturn();

        // After return
        //vdump($ret);
        return $ret;
    }

    /**
     * 环绕通知
     * @Around()
     *
     * @param ProceedingJoinPoint $proceedingJoinPoint
     *
     * @return mixed
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $a = $proceedingJoinPoint->getMethod();
        vdump($a);
        // Before around
        $result = $proceedingJoinPoint->proceed();
        // After around
        return $result;
    }

    /**
     * 异常通知
     * @param \Throwable $throwable
     *
     * @AfterThrowing()
     */
    public function afterThrowing(\Throwable $throwable)
    {
        vdump($throwable->getMessage());
    }
}