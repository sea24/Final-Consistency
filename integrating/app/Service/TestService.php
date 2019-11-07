<?php declare(strict_types=1);


namespace App\Service;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;

/**
 * Class ServiceContext
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class TestService
{
    /**
     * @param $data
     * @return bool
     */
    public function test($data): bool
    {
        echo "更新成功";
        return true;
    }
}