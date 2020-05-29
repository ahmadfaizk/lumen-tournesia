<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = array_map('str_getcsv', file(base_path() . '/database/seeds/csvs/provinces.csv'));
        foreach($datas as $data) {
            DB::table('provinces')->insert([
                'id' => $data[0],
                'name' => $data[1]
            ]);
        }
    }
}
