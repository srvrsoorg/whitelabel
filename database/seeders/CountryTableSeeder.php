<?php

namespace Database\Seeders;

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
            
            // Check if the file exists
            if (File::exists($filePath)) {
                // Read the SQL file contents
                $sql = File::get($filePath);
                
                // Split the SQL statements by semicolon
                $statements = array_filter(explode(';', $sql));

                // Execute each statement individually
                foreach ($statements as $statement) {
                    // Avoid running empty statements
                    if (trim($statement) != '') {
                        DB::unprepared($statement . ';');
                    }
                }
                
                $this->command->info('Countries imported successfully!');
            } else {
                throw new \Exception('SQL file not found at: ' . $filePath);
            }
        } catch (\Exception $e) {
            // Log the error and show an error message
            report($e);
            $this->command->error('Failed to import countries: ' . $e->getMessage());
        }
    }
}

