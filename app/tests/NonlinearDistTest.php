<?php

class NonlinearDistTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testWeightedDistribution()
	{
        $results = [];
		$nld = \RedisDemo\Helpers\NonlinearDistribution::weighted(0, 10, 5, 0.3);
        for($i = 0; $i < 10000; $i++) {
            $random = intval(floor($nld->run()));
            if(!array_key_exists($random, $results))
            {
                $results[$random] = 0;
            }
            $results[$random]++;
            ksort($results);
            echo "\r";
            array_walk($results, function($r, $idx) {
                echo "$idx: $r | ";
            });
        }
        $this->assertTrue(true);
	}

}
