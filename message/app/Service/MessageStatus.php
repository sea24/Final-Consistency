<?php declare(strict_types=1);

namespace App\Service;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class MessageStatus
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class MessageStatus
{
    //进入预发送系统，未操作投递
    const STATUS_UNDELIVERED = 1;

    //已投递，未被业务消费
    const STATUS_UNCONSUMED = 2;
}