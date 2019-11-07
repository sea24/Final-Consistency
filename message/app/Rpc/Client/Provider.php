<?php
/**
 * 匹配地址
 */

namespace App\Rpc\Client;

use Swoft\Rpc\Client\Contract\ProviderInterface;
use App\Components\LoadBalance\RandLoadBalance;

class Provider implements ProviderInterface
{
    protected $serviceName;
    protected $consulConfig;

    public function __construct($serviceName, $consulConfig)
    {
        $this->serviceName = $serviceName;
        $this->consulConfig = $consulConfig;
    }

    public function getList($client): array
    {
        $config = bean('config')->get((string)$this->consulConfig);

        $address = bean('consulProvider')->getServerList($this->serviceName, $config);
        if(empty($address)){
            vdump('获取服务失败');
        }
        $address = RandLoadBalance::select(array_values($address))['address'];
        return [$address];
    }
}