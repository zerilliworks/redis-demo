<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 7/28/14
// Time: 11:19 PM
// For: Redis Demo


use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {

    protected $faker;

    function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->faker->seed(12466499944354);
    }


    public function run()
    {
        $totalUsers = User::count();
        if(DB::table('test_friends')->count() >= $totalUsers) {
            echo "Already ";
            return;
        }
        $i = 0;
        while ($i < $totalUsers) {
            $i++;
            // Give each user between 5 and 100 friends

            echo "\rInserted $i users out of $totalUsers (mem ".memory_get_usage().")";
            unset($newbie);
        }
        echo PHP_EOL;
    }

}