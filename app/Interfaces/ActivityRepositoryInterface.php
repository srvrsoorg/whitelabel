<?php

namespace App\Interfaces;

interface ActivityRepositoryInterface 
{
    public function getUserActivities($user);
}