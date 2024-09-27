<?php

namespace App\Models\Admin\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use Cache;

class Payment extends Model
{
    use HasFactory, DateTime;

    protected $table = "payment_configurations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider',
        'mode',
        'client_id',
        'client_secret',
        'enabled',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when UserServer is saved or updated
        static::saved(function () {
            Cache::flush();
        });

        // Clear cache when UserServer is deleted
        static::deleted(function () {
            Cache::flush();
        });
    }
}
