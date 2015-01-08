<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/4/14
// Time: 10:07 AM
// For: Redis Demo


namespace RedisDemo\Helpers;


class NonlinearDistribution {

    protected $method;

    public function __construct($m)
    {
        $this->method = $m;
    }

    public static function weighted($lowerRange, $upperRange, $median, $falloff)
    {
        return new self(function() use ($lowerRange, $upperRange, $median, $falloff) {
            // Remap upper and lower ranges to 0 thru 1
            $l = 0;
            $u = 1;
            $med = $median - $lowerRange;
            $med /= $upperRange - $lowerRange;
            $fo = $falloff / ($upperRange - $lowerRange);
            $fo += 1;

            $point = $mrand = mt_rand() / mt_getrandmax();   // Random floating-point value
            $adist = abs($med - $mrand);
            $dist = $med - $mrand;
            $point -= $dist / $fo;

            return $point * $upperRange + $lowerRange;
        });
    }

    public static function falloff($lowerRange, $upperRange, $falloff)
    {

    }

    public static function exponential($lowerRange, $upperRange, $exp)
    {

    }

    public function run()
    {
        return call_user_func($this->method);
    }

} 