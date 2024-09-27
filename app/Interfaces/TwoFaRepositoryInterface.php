<?php

namespace App\Interfaces;


interface TwoFaRepositoryInterface 
{
    public function getTwoFa($user);
    public function getBackupCode($user);
    public function getRegeneratedBackupCode($user);
    public function sendBackupCode($user);
}