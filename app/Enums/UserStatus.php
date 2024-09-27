<?php 

namespace App\Enums;

use ArchTech\Enums\Values;
use ArchTech\Enums\InvokableCases;

enum UserStatus: string
{
	use InvokableCases, Values;

	case ACTIVE = 'active';
	case BANNED = 'banned';
	case PENDING = 'pending';
	case LOCKED = 'locked';
}