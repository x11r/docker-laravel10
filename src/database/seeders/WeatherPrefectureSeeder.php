<?php

namespace Database\Seeders;

use App\Models\WeatherPrefecture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeatherPrefectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	    (new WeatherPrefecture)->fill(['id' => '44', 'name' => '東京'])->save();
	    (new WeatherPrefecture)->fill(['id' => '85', 'name' => '福岡'])->save();
	    (new WeatherPrefecture)->fill(['id' => '82', 'name' => '佐賀'])->save();
    }
}
