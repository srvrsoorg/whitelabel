<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $filePath = base_path('database/world.sql');
            
            if($filePath){
                $sql = File::get($filePath);
                DB::unprepared($sql);
            }else{
                throw new \Exception('SQL file not found');
            }
        }catch (\Exception $e) {
            report($e);
        }
    }
}
