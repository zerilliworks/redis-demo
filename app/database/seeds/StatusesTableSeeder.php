<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 7/28/14
// Time: 11:19 PM
// For: Redis Demo


use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder {

    protected $faker;

    function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->faker->seed(12466499944354);
    }

    public function batchUsers()
    {
        $userCount = User::count();

        for($i = 0; $i < $userCount; $i++) {
            yield User::skip($i)->take(1)->first();
        }
    }


    public function run()
    {
        if(Status::count() >= 1) {
            echo "Already ";
            return;
        }
        $sc = 0;
        $uc = 1;
        // Users will have between 1 and 30 statuses.
        foreach($this->batchUsers() as $user) {
            $statuses = floor(rand(1, 30));
            for($s = 0; $s <= $statuses; $s++) {
                $user->statuses()->create([
                                              'body' => $this->faker->realText(500)
                                          ]);
                $sc++;
                echo "\rInserted $sc statuses on $uc users";
            }
            $uc++;
        }
        echo PHP_EOL;

    }

}