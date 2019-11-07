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
    protected  $configName; //服务配置文件
    public function getProvider(): ?ProviderInterface
    {
        //不能区分当前调用的服务是哪个
         return $this->provider=new Provider($this->getServiceName(),$this->configName);
    }
    /*
     * 获取服务名称
     */
    public  function  getServiceName(){
        return $this->serviceName;
    }

    /*
     * 获取服务配置文件
     */
    public  function  getConfigName(){
        return $this->configName;
    }
}