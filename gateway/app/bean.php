<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

use App\Common\DbSelector;
use App\Process\MonitorProcess;
use Swoft\Crontab\Process\CrontabProcess;
use Swoft\Db\Pool;
use Swoft\Http\Server\HttpServer;
use Swoft\Task\Swoole\SyncTaskListener;
use Swoft\Task\Swoole\TaskListener;
use Swoft\Task\Swoole\FinishListener;
use Swoft\Rpc\Client\Client as ServiceClient;
use Swoft\Rpc\Client\Pool as ServicePool;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Http\Server\Swoole\RequestListener;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoft\Server\SwooleEvent;
use Swoft\Db\Database;
use Swoft\Redis\RedisDb;

return [
    'noticeHandler' => [
        'logFile' => '@runtime/logs/notice-%d{Y-m-d-H}.log',
    ],
    'applicationHandler' => [
        'logFile' => '@runtime/logs/error-%d{Y-m-d}.log',
    ],
    'logger' => [
        'flushRequest' => false,
        'enable' => false,
        'json' => false,
    ],
    'httpServer' => [
        'class' => HttpServer::class,
        'port' => 9501,
        'listener' => [
            'rpc' => bean('rpcServer')
        ],
        'process' => [
//            'monitor' => bean(MonitorProcess::class)
//            'crontab' => bean(CrontabProcess::class)
        ],
        'on' => [
//            SwooleEvent::TASK   => bean(SyncTaskListener::class),  // Enable sync task
            SwooleEvent::TASK => bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => bean(FinishListener::class)
        ],
        /* @see HttpServer::$setting */
        'setting' => [
            'task_worker_num' => 12,
            'task_enable_coroutine' => true,
            'worker_num' => 10,
            'max_connection' => 10000,
            'reactor_num' => 4,
        ]
    ],
    'httpDispatcher' => [
        // Add global http middleware
        'middlewares' => [
            //\App\Http\Middleware\FavIconMiddleware::class,
            // \Swoft\Whoops\WhoopsMiddleware::class,
            // Allow use @View tag
            //\Swoft\View\Middleware\ViewMiddleware::class,
        ],
        'afterMiddlewares' => [
            \Swoft\Http\Server\Middleware\ValidatorMiddleware::class
        ]
    ],
    'db' => [
        'class' => Database::class,
        'dsn' => 'mysql:dbname=test;host=127.0.0.1',
        'username' => 'root',
        'password' => 'swoft123456',
    ],
    'db2' => [
        'class' => Database::class,
        'dsn' => 'mysql:dbname=test2;host=127.0.0.1',
        'username' => 'root',
        'password' => 'swoft123456',
//        'dbSelector' => bean(DbSelector::class)
    ],
    'db2.pool' => [
        'class' => Pool::class,
        'database' => bean('db2'),
    ],
    'db3' => [
        'class' => Database::class,
        'dsn' => 'mysql:dbname=test2;host=127.0.0.1',
        'username' => 'root',
        'password' => 'swoft123456'
    ],
    'db3.pool' => [
        'class' => Pool::class,
        'database' => bean('db3')
    ],
    'migrationManager' => [
        'migrationPath' => '@database/Migration',
    ],
    'redis' => [
        'class' => RedisDb::class,
        'host' => '134.175.221.102',
        'port' => 6379,
        'database' => 1,
        'password' => 'yanghailong',
        'option' => [
            'prefix' => 'Fusion:'
        ]
    ],
    'user' => [
        'class' => App\Rpc\Client\Client::class,
        'serviceName' => 'meiquick',
        'setting' => [
            'timeout' => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout' => 10.0,
            'read_timeout' => 0.5,
        ],
        'packet' => bean('rpcClientPacket')
    ],
    'user.pool' => [
        'class' => ServicePool::class,
        'client' => bean('user'),
        'minActive' => 5,
        'maxActive' => 100,
        'maxWait' => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 0,
    ],
    'order' => [
        'class' => App\Rpc\Client\Client::class,
        'serviceName' => 'rabbitMq-order',
        'configName' => 'pay.consul',
        'setting' => [
            'timeout' => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout' => 10.0,
            'read_timeout' => 0.5,
        ],
        'packet' => bean('rpcClientPacket')
    ],
    'order.pool' => [
        'class' => ServicePool::class,
        'client' => bean('order'),
        'minActive' => 5,
        'maxActive' => 100,
        'maxWait' => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 0,
    ],
    'message' => [
        'class' => App\Rpc\Client\Client::class,
        'serviceName' => 'rabbitMq-message',
        'configName' => 'message.consul',
        'setting' => [
            'timeout' => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout' => 10.0,
            'read_timeout' => 0.5,
        ],
        'packet' => bean('rpcClientPacket')
    ],
    'message.pool' => [
        'class' => ServicePool::class,
        'client' => bean('message'),
        'minActive' => 5,
        'maxActive' => 100,
        'maxWait' => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 0,
    ],
    'rpcServer' => [
        'class' => ServiceServer::class,
        'port' => 9502,
    ],
    'wsServer' => [
        'class' => WebSocketServer::class,
        'port' => 18308,
        'on' => [
            // Enable http handle
            SwooleEvent::REQUEST => bean(RequestListener::class),
        ],
        'debug' => 1,
        // 'debug'   => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting' => [
            'log_file' => alias('@runtime/swoole.log'),
        ],
    ],
    'tcpServer' => [
        'port' => 18309,
        'debug' => 1,
    ],
    /** @see \Swoft\Tcp\Protocol */
    'tcpServerProtocol' => [
        // 'type'            => \Swoft\Tcp\Packer\JsonPacker::TYPE,
        'type' => \Swoft\Tcp\Packer\SimpleTokenPacker::TYPE,
        // 'openLengthCheck' => true,
    ],
    'cliRouter' => [
        // 'disabledGroups' => ['demo', 'test'],
    ],

    /**
     * 事件监听
     */
    'consulProvider' => [
        'class' => \App\Components\Consul\ConsulProvider::class
    ],
];
