<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = array_map('str_getcsv', file(base_path() . '/database/seeds/csvs/regencies.csv'));
        foreach($datas as $data) {
            DB::table('regencies')->insert([
                'id' => $data[0],
                'id_province' => $data[1],
                'name' => $data[2]
            ]);
        }
    }
}
