<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Taman'],
            ['name' => 'Pantai'],
            ['name' => 'Gunung'],
            ['name' => 'Candi'],
            ['name' => 'Monumen'],
            ['name' => 'Landmark'],
            ['name' => 'Gua'],
            ['name' => 'Bukit'],
            ['name' => 'Air Terjun'],
            ['name' => 'Museum'],
            ['name' => 'Wisata Religi']
        ]);
    }
}
