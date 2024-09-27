<?php

namespace App\Interfaces\UsageSummary;

interface UsageSummaryRepositoryInterface
{
	public function getUsageSummary($request, $user);
}