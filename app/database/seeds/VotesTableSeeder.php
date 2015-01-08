<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 7/28/14
// Time: 11:19 PM
// For: Redis Demo


use Illuminate\Database\Seeder;

class VotesTableSeeder extends Seeder {

    protected $faker;

    function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->faker->seed(12466499944354);
    }

    public function batchStatuses()
    {
        $statusCount = Status::count();

        for ($i = 0; $i < $statusCount; $i++) {
            yield Status::skip($i)->take(1)->first();
        }
    }


    public function run()
    {
        if(Vote::count() >= 1) {
            echo "Already ";
            return;
        }
        $userCount = User::count();
        $statusCount = Status::count();
        $uc = 1;
        $sc = 1;
        $vc = 0;
        // Statuses will have between 1 and 100 votes
        foreach ($this->batchStatuses() as $status) {
            $votes = floor(rand(1, 100));
            for ($v = 0; $v <= $votes; $v++) {
                $status->votes()->create([
                                             'user_id' => floor(rand(1, $userCount)),
                                             'value'   => $this->faker->optional(0.8, -1)->randomElement([1, 1])
                                         ]);
                $vc++;
                echo "\rInserted $vc votes on $sc statuses out of $statusCount";
            }
            $sc++;
        }
        echo PHP_EOL;
    }

}