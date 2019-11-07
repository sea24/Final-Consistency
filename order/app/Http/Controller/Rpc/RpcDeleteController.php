<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller\Rpc;

//use App\Rpc\Lib\UserInterface;
use MeiQuick\Rpc\Lib\UserInterface;
use Exception;
use Swoft\Co;
use Swoft\Exception\SwoftException;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class RpcController
 *
 * @since 2.0
 *
 * @Controller("rpcDelete")
 */
class RpcDeleteController
{
    /**
     * @Reference(pool="user.pool", fallback="testdelete")
     *
     * @var UserInterface
     */
    private $userService;

    /**
     * @RequestMapping("sendBigContents")
     *
     * @return array
     */
    public function sendBigContent(): array
    {
        $result = $this->userService->sendBigContent('adb');
        // $result2 = $this->userService2->getList(12, 'type');
        return [$result];
    }

}
