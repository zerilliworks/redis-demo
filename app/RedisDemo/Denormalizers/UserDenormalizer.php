<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 9:26 AM
// For: Redis Demo


namespace RedisDemo\Denormalizers;
use Illuminate\Support\Facades\Redis;


class UserDenormalizer {

    protected $redis;

    function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function created($user)
    {
        // Insert the new data into Redis upon user creation
        $this->redis->hMset('users:'.$user->id, array_merge($user->toArray(), $user->attributesArray()));
    }

    public function deleted($user)
    {
        // Remove the user and their data
        $this->redis->del('users:'.$user->id);
    }

    public function updated($user)
    {
        // Update user details
        $this->created($user);
    }

    public function restored($user)
    {
        // Restore user from soft delete
        $this->created($user);
    }
} 