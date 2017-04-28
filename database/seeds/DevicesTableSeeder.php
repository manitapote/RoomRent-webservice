<?php

use Illuminate\Database\Seeder;

class DevicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('devices')->insert([
            'device_type' => 1,
            'user_id' => 1,
            'device_token' => str_random(60),
            'api_token' => str_random(60),
            ]);
    }
}
