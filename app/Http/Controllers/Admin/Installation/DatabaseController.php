<?php

namespace App\Http\Controllers\Admin\Installation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Database;
use DB;
use Artisan;
use Exception;

class DatabaseController extends Controller
{
    /**
     * Retrieve and return the database configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Retrieve the first database configuration and return it as JSON
            return response()->json([
                'database' => Database::first()
            ]);

        } catch (Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Store a new database configuration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'host' => 'required|max:255',
            'database' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        try {

            // Prepare the database configuration array
            $config = [
                'host' => $request->host,
                'database' => $request->database,
                'username' => $request->username,
                'password' => $request->password,
            ];
            
            // Set database credentials
            $this->setDatabaseCredential($config);

            Artisan::call("cache:clear");  // Clear the application cache
            Artisan::call("config:cache");  // Re-cache the configuration files
            Artisan::call("config:clear");  // Clear the configuration cache
            Artisan::call("queue:restart"); // Queue restart

            // Check Runtime Database Connection
            $this->checkDatabaseConnection($config);

            // Run migration command 
            $this->migrate();

            // Check if database exists 
            if(Database::exists()) {
                Database::first()->delete();
            }
            
            // Create a new Database model instance
            Database::create($config);

            // ✅ Success response: Database configuration saved successfully
            return response()->json([
                'message' => "Database has been configured successfully."
            ], 200);

        } catch (Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Database connection failed! Please verify your database credentials!"
            ], 500);
        }
    }

    /**
     * Check the database connection using the runtime configuration.
     *
     * @param array $config The database configuration array
     * @return void
     */
    public function checkDatabaseConnection($config) {

        // Establish a connection using the runtime configuration
        DB::connection()->getPdo();
        DB::connection()->getDatabaseName();
    }

    /**
     * Set the database credentials in the environment file.
     *
     * @param array $config The database configuration array
     * @return void
     */
    public function setDatabaseCredential($config) {

        Artisan::call("env:set db_host {$config['host']}");
        Artisan::call("env:set db_database {$config['database']}");
        Artisan::call("env:set db_username {$config['username']}");
        Artisan::call("env:set db_password {$config['password']}");
    }

    /**
     * Run database migrations with a rollback mechanism.
     *
     * @return void
     */
    public function migrate() {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Execute the migration command with --force to run on production
            Artisan::call("migrate:refresh --seed --force");
            
            // Capture and check the output from Artisan
            $artisanOutput = Artisan::output();

            // Throw an exception if any error is found in the Artisan output
            if (in_array("Error", str_split($artisanOutput, 5))) {
                throw new Exception($artisanOutput);
            }

            // Commit the transaction if no errors occurred
            DB::commit();

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}