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
        $totalUsers = 500;
        if(User::count() >= $totalUsers) {
            echo "Already ";
            return;
        }
        $i = 0;
        while ($i < $totalUsers) {
            $i++;
            $newbie = User::create([
                                                          'username'   => $this->faker->unique()->userName,
                                                          'email'      => $this->faker->unique()->email,
                                                          'password'   => Hash::make('everybody has the same password in this scenario.'),
                                                          'first_name' => $this->faker->firstName,
                                                          'last_name'  => $this->faker->lastName,
                                                          'join_date'  => $this->faker->dateTimeBetween('-4 years'),
                                                      ]);

            if($employer = $this->faker->optional(0.7)->company) {
                $newbie->addAttribute('employed', '1');
                $newbie->addAttribute('employer', $employer);
            }

            if($phone = $this->faker->optional(0.65)->phoneNumber) {
                $newbie->addAttribute('phone_number', $phone);
            }

            if($city = $this->faker->optional(0.8)->city) {
                $state = $this->faker->stateAbbr;
                $newbie->addAttribute('location', "$city, $state");
            }

            if($bio = $this->faker->optional(0.42)->realText()) {
                $newbie->addAttribute('bio', $bio);
            }

            $newbie->addAttribute('user_agent', $this->faker->userAgent);
            $newbie->addAttribute('ip_address', $this->faker->ipv4);

            echo "\rInserted $i users out of $totalUsers (mem ".memory_get_usage().")";
            unset($newbie);
        }
        echo PHP_EOL;
    }

}