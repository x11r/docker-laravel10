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
    protected $signature = 'app:weather-download {--prefecture=} {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'download weather data and import {prefecture: prefecture} {start : start} {end: end}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$prefecture = $this->option('prefecture');
	    $start = $this->option('start');
	    $end = $this->option('end');

		Log::debug(__LINE__ . ' ' . __METHOD__
			. ' [prefecture]' . $prefecture
			. ' [start] ' . $start
			. ' [end] ' . $end
		);

//		WeatherService::importDaily();
		WeatherService::downloadWeatherCsv($prefecture, $start, $end);
    }
}
