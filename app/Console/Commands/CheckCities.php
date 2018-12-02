<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\City;
use App\Http\Helpers\Helper;
use Carbon\Carbon;

class CheckCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckCities:checkcities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last_updated = City::max('updated_at');
        $url = Helper::getUrl();
        $server_updated = get_headers($url, 1)["Last-Modified"];
        $server_updated = Carbon::parse($server_updated);
        if ($server_updated->gt($last_updated)) {
            City::truncate();
            Helper::createCities();
        }
    }
}
