<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\BasicDetail;
use App\Http\Helper;

class OtherSettingController extends Controller
{
    /**
     * Display the list of policy settings (Terms, Privacy, Refund).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $policies = BasicDetail::whereIn('key', [
                'terms_condition', 'privacy_policy', 'refund_policy'
            ])->pluck('value', 'key');

            return response()->json($policies);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Store or update policy settings (Terms, Privacy, Refund).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'terms_condition' => 'nullable|string',
            'privacy_policy'  => 'nullable|string',
            'refund_policy'   => 'nullable|string',
        ]);

        try {
            $policies = [
                'terms_condition' => $request->terms_condition,
                'privacy_policy'  => $request->privacy_policy,
                'refund_policy'   => $request->refund_policy,
            ];

            foreach ($policies as $key => $value) {
                BasicDetail::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            
            Helper::adminActivity(auth()->user(), 'Link', 'Update', 'Policy links (Terms & Conditions, Privacy Policy, Refund Policy) have been updated.');

            return response()->json([
                'message' => 'Links updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!'
            ], 500);
        }
    }
}
