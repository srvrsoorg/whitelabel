<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\TwoFa;
use App\Models\User;
use App\Http\Helper;
use PragmaRX\Google2FAQRCode\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Interfaces\TwoFaRepositoryInterface;

class TwoFaController extends Controller
{
    /**
     * The TwoFa repository instance.
     *
     * @var TwoFaRepositoryInterface
     */
    private $twoFaRepository;

    /**
     * Create a new controller instance.
     *
     * @param TwoFaRepositoryInterface $twoFaRepository
     * @return void
     */
    public function __construct(TwoFaRepositoryInterface $twoFaRepository) 
    {
        $this->twoFaRepository = $twoFaRepository;
    }

    /**
     * Toggle two-factor authentication.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(User $user)
    {
        try {
            $twoFa = $this->twoFaRepository->getTwoFa($user);

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Account', 'Update', $twoFa['message']);

            // Return the status of two-factor authentication for the user
            return response()->json($twoFa);

        } catch (\Exception $e) {
            // Error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Show backup codes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function backupCodeShow(User $user)
    {
        try {

            // Return the backup codes for the user
            return response()->json($this->twoFaRepository->getBackupCode($user));

        } catch (\Exception $e) {
            // Error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Regenerate backup codes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function regenerateBackupCodes(User $user)
    {
        try {

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Account', 'Update', 'Backup codes have been regenerated.');

            // Return the regenerated backup codes for the user
            return response()->json($this->twoFaRepository->getRegeneratedBackupCode($user));

        } catch (\Exception $e) {
            // Error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Send backup codes via email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBackupCodeViaEmail(User $user)
    {
        try {

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Account', 'Update', 'Backup codes sent via email.');

            // Return the result of sending backup codes via email for the user
            return response()->json($this->twoFaRepository->sendBackupCode($user));
            
        } catch (\Exception $e) {
            // Log the exception
            report($e);
            // Error response in case of an exception
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}