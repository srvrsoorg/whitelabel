<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class BasicDetail extends Model
{
    use HasFactory, DateTime;

    protected $table = 'basic_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];
}
