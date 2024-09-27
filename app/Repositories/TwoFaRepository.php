<?php

namespace App\Repositories;

use App\Interfaces\TwoFaRepositoryInterface;
use App\Http\Helper;

class TwoFaRepository implements TwoFaRepositoryInterface 
{
    /**
     * Enable or disable two-factor authentication (2FA) for the given user.
     *
     * @param  \App\Models\User  $user  The user for whom to toggle 2FA.
     * @return array
     */
    public function getTwoFa($user)
    {
        $twoFa = !$user->two_fa_enable;

        // 2fa enable or disable
        $user->two_fa_enable = $twoFa;
        $user->save();

        if($twoFa) {
            $backupCodeCount = $user->backupCodes()->count();
            $totalCreateBackupCode = 6 - $backupCodeCount;

            for ($x = 1; $x <= $totalCreateBackupCode; $x++) {
                $user->backupCodes()->create(['backup_code' => Helper::generateUniqueBackupCode()]);
            }
        }

        $message = $twoFa?"enabled":"disabled";

        if(!$twoFa){
            $user->google2fa_enable = false;
            $user->save();
        }

        // Success response
        return [
            'two_fa' => $twoFa,
            'message' => "Two factor authentication has been ".$message."."
        ];
    }

    /**
     * Retrieve the backup codes and their usage status for the given user.
     *
     * @param  \App\Models\User  $user  The user whose backup codes to retrieve.
     * @return array
     */
    public function getBackupCode($user)
    {
        $data = null;

        $data['backup_codes'] = $user->backupCodes()->select(['backup_code', 'used'])->get()->toArray();

        // Backup Code date
        $cretedAt = $user->backupCodes()->first()->created_at;
        $data['generated_at']['date'] = date("M j, Y", strtotime($cretedAt));
        $data['generated_at']['time'] = date("g:i A", strtotime($cretedAt));

        $data['available'] = $user->backupCodes()->where('used', false)->count();
        $data['used'] = $user->backupCodes()->where('used', true)->count();

        // Success response
        return $data;
    }

    /**
     * Regenerate all backup codes for the given user.
     *
     * @param  \App\Models\User  $user  The user for whom to regenerate backup codes.
     * @return array
     */
    public function getRegeneratedBackupCode($user)
    {
        $user->backupCodes()->delete();

        for ($x = 1; $x <= 6; $x++) {
            $user->backupCodes()->create(['backup_code' => Helper::generateUniqueBackupCode()]);
        }

        $data['backup_codes'] = $user->backupCodes()->select(['backup_code', 'used'])->get()->toArray();

        // Backup Code date
        $cretedAt = $user->backupCodes()->first()->created_at;
        $data['generated_at']['date'] = date("M j, Y", strtotime($cretedAt));
        $data['generated_at']['time'] = date("g:i A", strtotime($cretedAt));

        $data['available'] = $user->backupCodes()->where('used', false)->count();
        $data['used'] = $user->backupCodes()->where('used', true)->count();

        return [
            "data" => $data,
            "message" => "Backup Codes regenerated successfully."
        ];
    }

    /**
     * Send the user's backup codes to their email.
     *
     * @param  \App\Models\User  $user  The user to whom the backup codes will be sent.
     * @return array
     */
    public function sendBackupCode($user)
    {
     $backupCodes = $user->backupCodes;  

        //send mail
        \Mail::to($user)->queue(( new \App\Mail\User\TfaBackupCode($user->name, $backupCodes))->onQueue('default'));

        // Success response
        return [
            "message" => "Backup Codes successfully send via E-Mail."
        ];
    }
}