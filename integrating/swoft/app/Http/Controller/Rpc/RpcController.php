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
 * @Controller("rpcs")
 */
class RpcController
{
    /**
     * @Reference(pool="user.pool", fallback="getLists")
     *
     * @var UserInterface
     */
    private $userService;

    /**
     * @Reference(pool="user.pool", fallback="testdelete")
     *
     * @var UserInterface
     */
    private $userServicedelete;

    /**
     * @Reference(pool="user.pool", fallback="delete")
     *
     * @var UserInterface
     */
    private $userService2;

    /**
     * @RequestMapping("getList")
     *
     * @return array
     */
    public function getListss(): array
    {
        echo 666
        $result = $this->userService->getList(12, 'type');
        // $result2 = $this->userService2->getList(12, 'type');
        return [$result];
    }

    /**
     * @RequestMapping("sendBigContents")
     *
     * @return array
     */
    public function sendBigContent(): array
    {
        $result = $this->userServicedelete->sendBigContent('adb');
        // $result2 = $this->userService2->getList(12, 'type');
        return [$result];
    }

    /**
     * @RequestMapping("delete")
     *
     * @return array
     */
    public function delete() : array
    {
        $result = $this->userService2->sendBigContent('abc');
        // $result2 = $this->userService2->getList(12, 'type');
        return [$result];
    }

    /**
     * @RequestMapping("returnBool")
     *
     * @return array
     */
    public function returnBool(): array
    {
        $result = $this->userService->delete(12);

        if (is_bool($result)) {
            return ['bool'];
        }

        return ['notBool'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     */
    public function bigString(): array
    {
        $string = $this->userService->getBigContent();

        return ['string', strlen($string)];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws SwoftException
     */
    public function sendBigString(): array
    {
        $content = Co::readFile(__DIR__ . '/../../Rpc/Service/big.data');

        $len = strlen($content);
        $result = $this->userService->sendBigContent($content);
        return [$len, $result];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     */
    public function returnNull(): array
    {
        $this->userService->returnNull();
        return [null];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     *
     * @throws Exception
     */
    public function exception(): array
    {
        $this->userService->exception();

        return ['exception'];
    }

}
