<?php
/**
 * 加权随机算法
 */

namespace App\Components\LoadBalance;

class RandLoadBalance implements LoadBalanceInterface
{
    public static function select(array $serviceList): array
    {
        $sum = 0; //总的权重值
        $weightsList = [];
        foreach ($serviceList as $k => $v) {
            $sum += $v['weight'];
            $weightsList[$k] = $sum;
        }
        $rand = mt_rand(0, $sum);
        foreach ($weightsList as $k => $v) {
            if ($rand <= $v) {
                return $serviceList[$k];
            }
        }
    }
}