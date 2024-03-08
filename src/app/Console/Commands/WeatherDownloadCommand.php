<?php

namespace App\Console\Commands;

use App\Services\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WeatherDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weather-download {start} {end}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'download weather data and import {start : start} {end: end}';

    /**
     * Execute the console command.
     */
    public function handle()
    {

	    $start = $this->argument('start');
	    $end = $this->argument('end');
		Log::debug(__LINE__ . ' ' . __METHOD__ . ' [start] ' );

//		WeatherService::importDaily();
		WeatherService::downloadWeatherCsv($start, $end);
    }
}
