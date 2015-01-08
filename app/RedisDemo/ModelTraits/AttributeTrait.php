<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/2/14
// Time: 6:39 PM
// For: Redis Demo


namespace RedisDemo\ModelTraits;


trait AttributeTrait {

    public function attributes()
    {
        return $this->morphMany('Attribute', 'entity');
    }

    public function fetchAttribute($attr_key)
    {
        return $this->attributes()->whereAttribute($attr_key)->pluck('value');
    }

    public function putAttribute($attr_key, $attr_value)
    {
        return $this->attributes()->whereAttribute($attr_key)->update(['value' => $attr_value]);
    }

    public function addAttribute($attr_key, $attr_value = null)
    {
        return $this->attributes()->create(['attribute' => $attr_key, 'value' => $attr_value]);
    }

    public function removeAttribute($attr_key)
    {
        return $this->attributes()->whereAttribute($attr_key)->delete();
    }

    public function attributesArray()
    {
        $result = [];
        foreach ($this->attributes()->get() as $attr) {
            $result[$attr->attribute] = $attr->value;
        }

        return $result;
    }

}