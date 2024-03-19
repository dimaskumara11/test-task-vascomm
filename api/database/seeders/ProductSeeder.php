<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("product")->insert([[
            "code" => "SPT-00001",
            "name" => "sepatu",
            "description" => "lorem ipsum bla bla bla ...",
            "price" => 150000,
            "image" => "sepatu.jpg",
        ], [
            "code" => "KCM-00001",
            "name" => "kacamata",
            "description" => "lorem ipsum bla bla bla ...",
            "price" => 180000,
            "image" => "kacamata.png",
        ]]);
    }
}
