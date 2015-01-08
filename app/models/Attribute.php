<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/2/14
// Time: 6:43 PM
// For: Redis Demo


class Attribute extends Eloquent {

    protected $fillable = ['attribute', 'value'];
    protected $table = 'test_attributes';

    public function entity()
    {
        $this->morphTo();
    }

} 