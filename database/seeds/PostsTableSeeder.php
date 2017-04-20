<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker\Factory::create();
    	
        DB::table('posts')->insert([
        	'user_id' => 1,
        	'title' => $faker->word,
        	'location' => $faker->word,
        	'latitude' => -10.45,
        	'longitude' => 120.34343,
        	'price' => $faker->numberBetween(1000,10000),
        	'post_description' => $faker->paragraph,
        	'no_of_rooms' => $faker->numberBetween(1,5),
        	'offer_or_ask' => 1,
        	]);
    }
}
