<?php

namespace App\Http\Helpers;

use ZanySoft\Zip\Zip;
use App\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Helper
{
    private static function getFile($path)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::getUrl());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
            $destination = $path . 'cities.zip';
            $file = fopen($destination, "w+");
            fputs($file, $data);
            fclose($file);
            $zip = Zip::open($path . 'cities.zip');
            $zip->extract($path);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public static function getUrl()
    {
        return 'http://download.geonames.org/export/dump/RU.zip';
    }

    public static function createCities()
    {
        $path = base_path() . '/resources/cities/';
        self::getFile($path);
        $result = fopen($path . 'RU.txt', "r");
        DB::beginTransaction();
        try {
            $data = [];
            $i = 0;
            ini_set('max_execution_time', 300);
            while (($line = fgets($result)) !== false) {
                $line = trim($line);
                $rows = explode("\t", $line);
                $data[] = [
                    "id" => $rows[0],
                    "name" => $rows[1],
                    "name2" => $rows[2],
                    "name3" => $rows[3],
                    "latitude" => $rows[4],
                    "longitude" => $rows[5],
                    "country" => $rows[8],
                    "num1" => ($rows[10] || $rows[10] === "0") ? intval($rows[10]) : null,
                    "num2" => ($rows[14] || $rows[14] === "0") ? intval($rows[14]) : null,
                    "num3" => ($rows[16] || $rows[16] === "0") ? intval($rows[16]) : null,
                    "zone" => $rows[17],
                    "date" => $rows[18],
                    "updated_at" => Carbon::now()
                ];
                if ($i && $i % 500 === 0) {
                    City::insert($data);
                    unset($data);
                }
                $i++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            exit;
        }
    }

    public static function updateCities()
    {
        $path = base_path() . '/resources/cities/';
        self::getFile($path);
        $result = fopen($path . 'RU.txt', "r");
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            while (($line = fgets($result)) !== false) {
                $line = trim($line);
                $rows = explode("\t", $line);
                $changedCurrentRow = [
                    "id" => $rows[0],
                    "name" => $rows[1],
                    "name2" => $rows[2],
                    "name3" => $rows[3],
                    "latitude" => $rows[4],
                    "longitude" => $rows[5],
                    "country" => $rows[8],
                    "num1" => ($rows[10] || $rows[10] === "0") ? intval($rows[10]) : null,
                    "num2" => ($rows[14] || $rows[14] === "0") ? intval($rows[14]) : null,
                    "num3" => ($rows[16] || $rows[16] === "0") ? intval($rows[16]) : null,
                    "zone" => $rows[17],
                    "date" => Carbon::parse($rows[18]),
                ];
                $currentRow = City::find($rows[0]);
                if ($currentRow) {
                    $currentRow = $currentRow->toArray();
                    unset($currentRow['created_at']);
                    unset($currentRow['updated_at']);
                    if (!empty(array_diff($currentRow, $changedCurrentRow))) {
                        City::find($rows[0])->update($changedCurrentRow);
                    }
                } else {
                    City::insert($changedCurrentRow);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            exit;
        }
    }
}