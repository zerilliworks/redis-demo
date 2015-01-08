<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/2/14
// Time: 7:11 PM
// For: Redis Demo


class Status extends Eloquent {
    protected $table = 'test_statuses';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function comments()
    {
        return $this->hasMany('Comment');
    }

    public function votes()
    {
        return $this->morphMany('Vote', 'voteable');
    }

} 