<?php

namespace App\Console\Commands;

use App\Services\WeatherImportService;
use App\Services\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WeatherImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weather-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'download weather data and import ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		WeatherService::ImportCsv();
    }

}
