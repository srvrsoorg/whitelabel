<?php

namespace App\Enums;

use ArchTech\Enums\Values;
use ArchTech\Enums\InvokableCases;

enum CloudProvider: string
{
    use InvokableCases, Values;

    case LIGHTSAIL = 'lightsail';
    case VULTR = 'vultr';
    case DIGITALOCEAN = 'digitalocean';
    case LINODE = 'linode';
    case HETZNER = 'hetzner';
}