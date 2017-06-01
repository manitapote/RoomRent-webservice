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
            'device_token' => 'dd9cl-vW_fY:APA91bH5eZ6kZJQnXl_w_2heLeu_xz3_YXh3prgrX3Iqmnjqo9r3afpTMOfzIOwXyKrQx_LK8ocebnI4MjJ2wRTnsr-HY85VpcVN_VwcfpzqJaIjW61L0ARWbhzw7O6nFrwe2ppLE-wQ',
            'api_token' => str_random(60),
            ]);
    }
}
