<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 9:30 AM
// For: Redis Demo


namespace RedisDemo\Denormalizers;
use Illuminate\Support\Facades\Redis;


class VoteDenormalizer {

    function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function recordVote($vote)
    {
        $votedModel = str_plural(strtolower($vote->voteable_type));
        $voteValue = $vote->value;

        $this->redis->pipeline(function($pipe) use ($vote, $votedModel, $voteValue)
        {
            // Increment or decrement the 'rank' field in the correct status hash
            switch ($voteValue) {
                case -1:
                    $pipe->hDecr($votedModel.':'.$vote->voteable_id, 'rank');
                    break;
                case 1:
                    $pipe->hIncr($votedModel.':'.$vote->voteable_id, 'rank');
                    break;
            }

            // Update the ranking
            $pipe->zIncrBy('statuses:ranked', $voteValue, $votedModel.':'.$vote->voteable_id);

        });

    }

} 