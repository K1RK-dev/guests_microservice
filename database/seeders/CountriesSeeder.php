<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(database_path('data/CountryCodes.json'));
        $countries = json_decode($json, true);
        $data = [];
        foreach($countries as $country){
            $data[] = [
                'dial_code' => $country['dial_code'],
                'name' => $country['name']
            ];
        }
        DB::table('countries')->insert($data);
    }
}
