<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
        	'username' => str_random(10),
        	'email' => str_random(10).'@gmail.com',
        	'password' => bcrypt('secret'),
        	'activation_token' => str_random(60),
        	'phone' => str_random(10),
        	]);
    }
}
