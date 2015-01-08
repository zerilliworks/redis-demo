<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 7/28/14
// Time: 11:19 PM
// For: Redis Demo


use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder {

    protected $faker;

    function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->faker->seed(12466499944354);
    }

    public function batchStatuses()
    {
        $statusCount = Status::count();

        for($i = 0; $i < $statusCount; $i++) {
            yield Status::skip($i)->take(1)->first();
        }
    }


    public function run()
    {
        if(Comment::count() >= 1) {
            echo "Already ";
            return;
        }
        $cc = 0;
        $sc = 1;
        $userCount = User::count();
        $statusCount = Status::count();
        // Users will have between 1 and 100 comments by a random user.
        foreach($this->batchStatuses() as $status) {
            $comments = floor(rand(1, 100));
            for($s = 0; $s <= $comments; $s++) {
                $status->comments()->create([
                                              'user_id' => floor(rand(1, $userCount)),
                                              'body' => $this->faker->realText(),
                                          ]);
                $cc++;
                echo "\rInserted $cc comments on $sc statuses of $statusCount";
            }
            $sc++;
        }
        echo PHP_EOL;

    }

}