<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/3/14
// Time: 12:36 AM
// For: Redis Demo


class Comment extends Eloquent {

    protected $table = 'test_comments';

    public function status()
    {
        return $this->belongsTo('Status');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

} 