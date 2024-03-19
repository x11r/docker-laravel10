<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_dailies', function (Blueprint $table) {
            $table->id();
			$table->date('date')->comment('年月日');
			$table->string('prefecture_id')->comment('都道府県名');
			$table->string('station_name')->comment('測定地名');
			$table->decimal('temperature_highest')->comment('最高気温（摂氏）');
			$table->decimal('temperature_lowest')->comment('最低気温（摂氏）');

			$table->unique(['date', 'prefecture_id', 'station_name']);
			$table->index(['date', 'station_name']);

            $table->timestamps();
			$table->comment('日別気象情報');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_dailies');
    }
};
