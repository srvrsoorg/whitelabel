<?php

namespace App\Http\Controllers\User;

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
    public function toggle()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            $twoFa = $this->twoFaRepository->getTwoFa($user);

            // Create activity
            Helper::createActivity(auth()->user(), 'Account', "Update", $twoFa['message']);

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
    public function backupCodeShow()
    {
        try {

            // Return the backup codes for the user
            return response()->json($this->twoFaRepository->getBackupCode(auth()->user()));

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
    public function regenerateBackupCodes()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Create activity
            Helper::createActivity($user, 'Account', "Update", "Backup codes have been regenerated.");

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
    public function sendBackupCodeViaEmail()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Create activity
            Helper::createActivity($user, 'Account', "Update", "Backup codes sent via email.");

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

    /**
     * Generate QR code for two-factor authentication.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function qrCode()
    {
        try {
            $user = auth()->user();

            if (!$user->google2fa_secret) {
                $user->update(['google2fa_secret' => Helper::generateUniqueGoogleSecret(32, 'users', 'google2fa_secret')]);
            }

            // Generate QR code URL
            $google2fa = new Google2FA();
            $google2fa_url = $google2fa->getQRCodeUrl(config('app.title'), $user->email, $user->google2fa_secret);

            // Generate SVG QR code
            $qrCodeSvg = QrCode::format('svg')->size(100)->generate($google2fa_url);

            // Success response
            return response()->json(["google2fa_url" => (string) $qrCodeSvg], 200);
        } catch (\Exception $e) {
            // Error response
            report($e);
            return response()->json(['message' => "Something went really wrong!"], 500);
        }
    }

    /**
     * Toggle Google two-factor authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleTwofaToggle(Request $request)
    {
        $request->validate([
            'verify_code' => 'nullable'
        ]);

        try {
            $user = auth()->user();

            if (!$user->google2fa_enable) {
                if (!$request->verify_code) {
                    return response()->json(["message" => "Verification code is required to enable Google two-factor authentication!"], 400);
                }

                $google2fa = new Google2FA();
                $valid = $google2fa->verifyKey($user->google2fa_secret, $request->verify_code, 0);
                
                if (!$valid) {
                    return response()->json(["message" => "Invalid verification code!"], 400);
                }

                $user->google2fa_enable = true;
                $status = "enabled";
            } else {
                $user->google2fa_enable = false;
                $status = "disabled";
            }

            $user->save();

            // Create activity log for toggling Google two-factor authentication
            $action = $status === 'enabled' ? 'Enable' : 'Disable';
            $message = "Google two-factor authentication $status successfully.";
            Helper::createActivity($user, 'Account', $action, "Google two-factor authentication has been $status.");

            // Success response
            return response()->json(["message" => $message], 200);
        } catch (\Exception $e) {
            // Error response
            report($e);
            return response()->json(['message' => "Something went really wrong!"], 500);
        }
    }

        // Two Factor Authentication verify
    public function twoFaVerify(Request $request)
    {
        // Request Validations
        $request->validate([
            'google_auth' => 'boolean',
            'email' => 'required|email',
            'code' => 'required|string'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if(!$user) {
            // Unauthorized response
            return response()->json([
                'message' => "The provided email is not registered!"
            ],401);
        }

        try {
            // Check if two-factor authentication is enabled for the user
            if(!$user->google2fa_enable && !$user->two_fa_enable) {
                // Error response
                return response()->json([
                    'message' => "You cannot perform this action!"
                ], 500);
            }

            $loginApproved = false;

            // Verify authentication code
            if($request->google_auth) {
                $google2fa = new Google2FA();
                $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code, 0);
                if($valid){
                    $loginApproved = true;
                }

                $backupCodeToken = $user->backupCodes()->where('backup_code',$request->code)->first();
                if($backupCodeToken && $backupCodeToken->used == 0 && $backupCodeToken->user && $backupCodeToken->user->email == $request->email) {
                    $backupCodeToken->used = true;
                    $backupCodeToken->save();

                    $loginApproved = true;
                }                
            } else {
                $twoFaToken = $user->twoFa()->where('token', $request->code)->first();
                if($twoFaToken && $twoFaToken->expires_at >= now() && $twoFaToken->user && $twoFaToken->user->email == $request->email) {
                    // Expire token
                    $twoFaToken->expires_at = now();
                    $twoFaToken->save();

                    $loginApproved = true;
                }

                $backupCodeToken = $user->backupCodes()->where('backup_code',$request->code)->first();
                if($backupCodeToken && $backupCodeToken->used == 0 && $backupCodeToken->user && $backupCodeToken->user->email == $request->email) {
                    $backupCodeToken->used = true;
                    $backupCodeToken->save();

                    $loginApproved = true;
                }
            }

            if($loginApproved) {
                $user->storeLogin();
                // Successful login response
                return response()->json([
                    'user' => $user,
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'tokenType' => "Bearer",
                    'message' => 'You have successfully logged in.'
                ], 200);
            }

            // Invalid code response
            return response()->json([
                'message' => "Invalid Code!"
            ], 500);
        } catch(\Exception $e) {
            // Error response
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Resend token verify
    public function twoFaResend(Request $request)
    {
        // Validations
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if(!$user) {
            // Unauthorized response
            return response()->json([
                'message' => "The provided email is not registered!"
            ], 401);
        }

        try {
            // Check if two-factor authentication is enabled for the user
            if(!$user->two_fa_enable) {
                // Error response
                return response()->json([
                    'message' => "You cannot perform this action!"
                ], 500);
            }

            $twoFaToken = TwoFa::where('user_id', $user->id)->first();

            if($twoFaToken && $twoFaToken->mail_limit < now()) {
                if($twoFaToken->expires_at >= now()) {
                    // Resend verification code via email
                    \Mail::to($user)->queue((new \App\Mail\User\TwoFactorAuthentication($user, $twoFaToken, request()->ip(), $request->userAgent()))->onQueue('default'));

                    // Update mail limit
                    $twoFaToken->mail_limit = now()->addMinute();
                    $twoFaToken->save();
                } else {
                    // Generate new verification code and resend
                    Helper::twoFactorAuthentication($user);
                }

                // Success response
                return response()->json([
                    'message' => "Verification code successfully re-sent to your registered email id."
                ], 200);
            }

            // Error response
            return response()->json([
                'message' => "Code already sent via E-Mail, Please wait up to 60 seconds!"
            ], 500);
        } catch(\Exception $e) {
            // Error response
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}