<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\City;

class CityController extends Controller
{
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            return $dist;
        }
    }

    public function index()
    {
        return view('cities');
    }

    public function search()
    {
        $result = City::distinct()->pluck('name')->toArray();
        return response()->json(['data' => json_encode($result)], 200);
    }

    public function getNearestCities(Request $request)
    {
        $currentCity = City::where('name', $request->city)->first();
        if ($currentCity) {
            $closestDistances = [];
            $closestLats = City::orderByRaw('ABS(latitude-' . $currentCity->latitude . ') ASC')->limit(2000)->get();
            $closestLongs = City::orderByRaw('ABS(longitude-' . $currentCity->longitude . ') ASC')->limit(2000)->get();
            $allClosest = $closestLats->merge($closestLongs);
            foreach ($allClosest as $city) {
                $closestDistances [] = ["id" => $city->id,
                    "distance" => $this->distance($currentCity->latitude, $currentCity->longitude, $city->latitude, $city->longitude)];
            }
            if (count($closestDistances) > 0) {
                usort($closestDistances, function ($item1, $item2) {
                    return $item1['distance'] <=> $item2['distance'];
                });
                $closestCities = array_chunk($closestDistances, 20)[0];
                $closestCitiesIds = array_column($closestCities, 'id');
                $cities = City::findMany($closestCitiesIds);
                return response()->json(['data' => json_encode($cities)], 200);
            }
            return response()->json(['data' => 'Something went wrong.'], 400);
        }
        return response()->json(['data' => 'City not found.'], 400);
    }
}
