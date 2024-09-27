<?php
namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait DateTime
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone(optional(auth()->user())->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone(optional(auth()->user())->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s')
        );
    }
}