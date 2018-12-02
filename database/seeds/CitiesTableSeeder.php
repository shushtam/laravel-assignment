<?php

use Illuminate\Database\Seeder;
use App\Http\Helpers\Helper;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Helper::createCities();
    }
}
