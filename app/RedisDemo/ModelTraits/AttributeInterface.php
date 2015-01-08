<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/2/14
// Time: 6:40 PM
// For: Redis Demo


namespace RedisDemo\ModelTraits;


interface AttributeInterface {

    public function attributes();

    public function fetchAttribute($attr_key);
    public function putAttribute($attr_key, $attr_value);
    public function addAttribute($attr_key, $attr_value = null);
    public function removeAttribute($attr_key);

} 