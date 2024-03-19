<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("user")->insert([[
            "name" => "admin",
            "role" => 1,
            "email" => "admin@admin.com",
            "password" => Hash::make("admin"),
        ], [
            "name" => "user",
            "role" => 2,
            "email" => "user@admin.com",
            "password" => Hash::make("user"),
        ]]);
    }
}
