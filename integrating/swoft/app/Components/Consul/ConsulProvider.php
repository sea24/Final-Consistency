<?php
/**
 * Created by PhpStorm.
 * User: Sea
 * Date: 2019/10/11
 * Time: 11:17
 */

namespace App\Components\Consul;

class ConsulProvider
{
    //v1/agent/services  展示所有的服务
    //v1/catalog/service/pay-php  某个服务的多个服务地址
    //v1/health/service/pay-php   某个服务的多个服务地址并且查看健康的状态
    const REGISTER_PATH = '/v1/agent/service/register'; //服务注册路径
    const HEALTH_PATH = '/v1/health/service/'; //获取健康服务

    public function registerServer($config)
    {
        //注册服务
        consulCurl($config['address'] . ':' . $config['port'] . self::REGISTER_PATH, "PUT", json_encode($config['register']));
        output()->writeln("<success>注册成功 Tcp=" . $config['address'] . ":" . $config['port'] . "</success>");
    }

    /**
     * 获取某个服务的列表
     */
    public function getServerList($serviceName, $config)
    {
        $query = [
            'dc' => $config['discovery']['dc']
        ];
        if (!empty($config['discovery']['tag'])) {
            $query['tag'] = $config['discovery']['tag'];
        }
        $queryStr = http_build_query($query);
        //排除不健康的服务,获取健康服务
        $url = $config['address'] . ':' . $config['port'] . self::HEALTH_PATH . $serviceName . '?' . $queryStr;
        //负载机制
        $serviceList = consulCurl($url, 'GET');

        $serviceList = json_decode($serviceList, true);
        $address = [];
        foreach ($serviceList as $k => $v) {
            //判断当前的服务是否是活跃的,并且是当前想要去查询服务
            foreach ($v['Checks'] as $c) {
                if ($c['ServiceName'] == $serviceName && $c['Status'] == "passing") {
                    $address[$k]['address'] = $v['Service']['Address'] . ":" . $v['Service']['Port'];
                    $address[$k]['weight'] = $v['Service']['Weights']['Passing'];
                }
            }
        }
        return $address;
    }
}