<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('users')->insert([
            'firstname' => $faker->name,
            'lastname' => $faker->name,
            'mobile' => rand(100000,999999),
            'password' => bcrypt('secret'),
            'birthdate' => date('Y-m-d'),
            'email' => $faker->unique()->safeEmail,
            'password' => bcrypt('secret'),
            'image'=> str_random(10),
            'national_id'=> str_random(10),
            'address'=> str_random(10),
            'lastlogin'=> date('Y-m-d H:i:s'),
        ]);
        //
    }
}
