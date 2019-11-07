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

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function getList($client): array
    {
        $config = bean('config')->get('pay.consul');
        $address = bean('consulProvider')->getServerList($this->serviceName, $config);
        $address = RandLoadBalance::select(array_values($address))['address'];
        return [$address];
    }
}