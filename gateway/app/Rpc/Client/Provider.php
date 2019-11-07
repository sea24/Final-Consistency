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
    protected $configName;

    public function __construct($serviceName, $configName)
    {
        $this->serviceName = $serviceName;
        $this->configName = $configName;
    }

    public function getList($client): array
    {
        $config = bean('config')->get((string)$this->configName);
        $address = bean('consulProvider')->getServerList($this->serviceName, $config);
        $address = RandLoadBalance::select(array_values($address))['address'];
        return [$address];
    }
}