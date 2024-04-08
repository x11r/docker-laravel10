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
	    Schema::create('weather_prefectures', function (Blueprint $table) {
			$table->id();
			$table->string('name')->comment('都道府県名');
			$table->timestamps();
			$table->comment('気象庁の都道府県コード一覧');
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_prefectures');
    }
};
