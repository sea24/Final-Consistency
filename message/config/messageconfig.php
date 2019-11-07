<?php

return [
    /*****************************************************Redis*********************************************************/
    /**
     * 消息系统Redis key
     */
    'redis_key' => [
        /*
         * 消息数据 (Hash)
         */
        'message_system' => 'message_system',

        /*
         * 消息超时未投递 (Hash)
         */
        'message_system_time' => 'message_system_time',

        /*
         * 投递超过最大次数放到队列，需要开启UI界面手动投递 (List)
         */
        'message_system_dead' => 'message_system_dead',

        /*
         * 主动方执行结果 (String)
         */
        'order_message_job' => 'order_message_job',

        /**
         * 手动投递UI界面
         */
        'message_ui_data' => 'message_ui_data',
    ],

    /**
     * 主动方Redis
     */
    'master_config' => [

        /*
         * 主业务消费完成,并把当前的消息msgId记录 ~ 被动方确认消费完成删除当前消息记录 (Hash)
         */
        'master_message_job' => 'master_message_job:',
    ],

    /**
     * 被动方Redis
     */
    'passive' => [

        /*
         * 消费中保证幂等性 (String)
         */
        'integrating_message_job' => 'integrating_message_job',
    ],

    /*****************************************************消息状态*********************************************************/
    /**
     * 消息处理状态
     */
    'message_status' => [

        /*
         * 消息状态子系统 (已经进入消息子系统但是未投递的，通常是消息系统问题）
         */
        'status_undelivered' => 1,

        /*
         * 已投递，超时没有被正确消息（消息恢复系统，通常是被动方问题）
         */
        'status_delivered' => 2,

        /*
         * 消息重新投递最大次数
         */
        'message_retries_number' => 5,
    ],

    /**
     * 定时器配置
     */
    'event' => [
        /*
         * 消息超时时长，单位：s
         */
        'message_out_time' => 30,

        /*
         * 定时器时间 单位：ms
         */
        'timer_tick' => 30000,
    ],
];