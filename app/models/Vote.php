<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 12:53 AM
// For: Redis Demo


class Vote extends Eloquent {

    protected $table = 'test_votes';

    public function voteable()
    {
        return $this->morphTo();
    }
} 