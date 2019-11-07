<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/25
 * Time: 22:45
 */

namespace App\Rpc\Client;

use Swoft\Rpc\Client\Contract\ProviderInterface;
use Swoft\Rpc\Client\Client as ClientSwoft;

class Client extends  ClientSwoft
{
    protected  $serviceName; //服务名称
    public function getProvider(): ?ProviderInterface
    {
        //不能区分当前调用的服务是哪个
         return $this->provider=new Provider($this->getServiceName());
    }
    /*
     * 获取服务名称
     */
    public  function  getServiceName(){
        return $this->serviceName;
    }
}