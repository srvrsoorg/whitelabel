<?php

namespace App\Http\Controllers\Admin\Installation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\{Smtp, SiteSetting};
use App\Http\Helper;

class SmtpController extends Controller
{
    /**
     * Store or update SMTP configuration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer|digits_between:1,6',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'from_name' => 'required|string|max:255|regex:/^[\p{L}\s]+$/u',
            'from_email' => 'required|email|max:255',
            'encryption' => 'required|string|max:255'
        ]);

        try {
            // Check if site settings exist and if no user is authenticated
            if(SiteSetting::exists() && !auth()->user()) {
                return response()->json([
                    'message' => "You cannot set up the smtp after a full setup!",
                ], 500);
            }

            // Check if SMTP configuration exists
            if(Smtp::exists()) {
                Smtp::first()->delete();
            }
            
            // Create a new Smtp model instance
            Smtp::create($request->post());            

            // Restart the queue worker for changes to take effect
            \Artisan::call('queue:restart');

            // Store Activity
            if(auth()->user()) {
                Helper::adminActivity(auth()->user(), 'Smtp', 'Update', 'SMTP details have been updated.');
            }

            // ✅ Success response: SMTP configuration has been created/updated successfully
            return response()->json([
                'message' => "SMTP configuration has been created successfully."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Retrieve and return SMTP configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Return SMTP configuration as JSON
            return response()->json([
                'smtp' => Smtp::first()
            ]);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Send a test email to verify SMTP configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function testMail()
    {
        try {
            // Retrieved authenticated user
            $user = auth()->user();

            // Check if SMTP configuration exists
            if (!Smtp::exists()) {
                return response()->json([
                    'message' => "SMTP details not found!"
                ], 500);
            }

            // Send a test email
            \Mail::to($user->email)->send(new \App\Mail\User\SendTestMail($user));

            // ✅ Success response: Test mail sent successfully
            return response()->json([
                'message' => "Test mail sent successfully."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => $e->getMessage() ? $e->getMessage() : "Something went really wrong!"
            ], 500);
        }
    }
}