<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 9:30 AM
// For: Redis Demo


namespace RedisDemo\Denormalizers;
use Illuminate\Support\Facades\Redis;


class StatusDenormalizer {
    protected $redis;

    function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function created($status)
    {
        $rank = $status->votes()->sum('value');

        $this->redis->pipeline(function($pipe) use ($rank, $status)
        {
            $pipe->hMset('statuses:'.$status->id, $status->toArray());
            $pipe->sAdd('statuses', 'statuses:'.$status->id);
            $pipe->sAdd('users:'.$status->user->id.':statuses', 'statuses:'.$status->id);
            $pipe->hSet('statuses:'.$status->id, 'rating', $rank);
            $pipe->zAdd('statuses:ranked', $rank, 'statuses:'.$status->id);
        });
    }

    public function deleted($status)
    {
        $this->redis->pipeline(function($pipe) use ($status)
        {
            $pipe->del('statuses:'.$status->id);
            $pipe->sRem('statuses', 'statuses:'.$status->id);
            $pipe->sRem('users:'.$status->user->id.':statuses', 'statuses:'.$status->id);
            $pipe->zRem('statuses:ranked', 'statuses:'.$status->id);
        });
    }
} 