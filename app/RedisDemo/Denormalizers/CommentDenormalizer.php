<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 9:30 AM
// For: Redis Demo


namespace RedisDemo\Denormalizers;
use Illuminate\Support\Facades\Redis;


class CommentDenormalizer {

    protected $redis;

    function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function created($comment)
    {
        $this->redis->pipeline(function($pipe) use ($comment)
        {
            $pipe->hMset('comments:'.$comment->id, array_only($comment->toArray(), ['user_id', 'status_id', 'body']));
            $pipe->sAdd('comments', 'comments:'.$comment->id);
            $pipe->sAdd('statuses:'.$comment->status_id.':comments', 'comments:'.$comment->id);
            $pipe->sAdd('statuses:'.$comment->status_id.':commenters', 'users:'.$comment->user_id);
            $pipe->sAdd('users:'.$comment->user->id.':comments', 'comments:'.$comment->id);
        });
    }

    public function deleted($comment)
    {
        $this->redis->pipeline(function($pipe) use ($comment)
        {
            $pipe->del('comments:'.$comment->id);
            $pipe->sRem('comments', 'comments:'.$comment->id);
            $pipe->sRem('statuses:'.$comment->status_id.':comments', 'comments:'.$comment->id);
            $pipe->sRem('users:'.$comment->user_id.':comments', 'comments:'.$comment->id);
        });

        if(!count($this->redis->sInter('statuses:'.$comment->status_id.':comments', 'users:'.$comment->user_id.':comments'))) {
            $this->redis->sRem('statuses:'.$comment->status_id.':commenters', 'users:'.$comment->user_id);
        }
    }
}