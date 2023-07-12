<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RakutenArea;
use App\Services\RakutenApiService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RakutenApiAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 一度全部削除
        RakutenArea::truncate();


		// 楽天APIから取得
	    $response = RakutenApiService::getAreas();
		$body = $response->body();
		$json = json_decode($body);

		$largeClass = $json->areaClasses->largeClasses[0]->largeClass;

		foreach ($largeClass[1]->middleClasses as $middleClass) {
            foreach ($middleClass->middleClass[1]->smallClasses as $smallClass) {

//                DB::enableQueryLog();
                if (isset($smallClasa->smallClass[1]->detailClasses)) {
                    foreach ($smallClass->smallClass[1]->detailClasses as $detailClass) {
                        $this->insertArea($largeClass[0], $middleClass->middleClass[0], $smallClass->smallClass[0], $detailClass->detailClass);

//                        /$sql = DB::getQueryLog();
//                        Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . print_r($sql, true));
                    }

                } else {
                    $this->insertArea($largeClass[0], $middleClass->middleClass[0], $smallClass->smallClass[0]);
                }
            }
		}
    }

    /**
     * @param object $largeClass
     * @param object $middleClass
     * @param object $smallClass
     * @param object|null $detailClass
     * @return void
     */
    private function insertArea(object $largeClass, object $middleClass, object $smallClass, object $detailClass = null): void
    {

        $insertParam = [
            'large_code' => $largeClass->largeClassCode,
            'large_name' => $largeClass->largeClassName,
            'middle_code' => $middleClass->middleClassCode,
            'middle_name' => $middleClass->middleClassName,
            'small_code' => $smallClass->smallClassCode,
            'small_name' => $smallClass->smallClassName,
        ];

        // 詳細エリア情報があったらセットする
        if ($detailClass) {
            $insertParam['detail_code'] = $detailClass->detailClassName;
            $insertParam['detail_name'] = $detailClass->detailClassName;
        }

        RakutenArea::create(
            $insertParam
        );

    }
}
