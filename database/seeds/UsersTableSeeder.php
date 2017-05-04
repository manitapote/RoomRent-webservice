<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'username' => "sameer",
        	'email' => 'sameer@gmail.com',
        	'password' => Hash::make("sameer"),
        	'activation_token' => str_random(60),
        	'phone' => str_random(10),
        	]);
    }
}
