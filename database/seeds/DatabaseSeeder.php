<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1;$i<=500;$i++):
        $this->call(UsersTableSeeder::class);
        endfor;
        // $this->call(UsersTableSeeder::class);
    }
}
