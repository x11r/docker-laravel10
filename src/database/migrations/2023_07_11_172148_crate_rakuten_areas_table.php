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
        Schema::create('rakuten_areas', function (Blueprint $table) {
			$table->comment('楽天APIのエリア一覧情報');
			$table->id();
			$table->string('large_code')->comment('国コード');
			$table->string('large_name')->comment('国名');;
			$table->string('middle_code')->comment('都道府県コード');
			$table->string('middle_name')->comment('都道府県名');
			$table->string('small_code')->comment('市区町村コード');
			$table->string('small_name')->comment('市区町村名');
			$table->string('detail_code')->nullable()->comment('詳細コード');
			$table->string('detail_name')->nullable()->comment('詳細名');
			$table->timestamps();
			$table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(('rakuten_areas'));
    }
};
