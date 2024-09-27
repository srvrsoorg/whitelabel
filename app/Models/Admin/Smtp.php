<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class Smtp extends Model
{
    use HasFactory, DateTime;

    const CACHE_KEYS = [
        "main" => "smtp-detail"
    ];

    protected $table = "smtps";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'from_email',
        'from_name',
        'encryption',
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Boot function to add events to clear cache
     */
    protected static function booted()
    {
        // Clear cache on create event
        static::created(function(){
            static::clearCache();
        });

        // Clear cache on update event
        static::updated(function(){
            static::clearCache();
        });

        // Clear cache on delete event
        static::deleted(function(){
            static::clearCache();
        });
    }

    /**
     * Clear cache for all defined cache keys
     */
    protected static function clearCache()
    {
        foreach(static::CACHE_KEYS as $value){
            \Cache::forget($value);
        }
    }
}
