<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class Database extends Model
{
    use HasFactory, DateTime;

    protected $table = "databases";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'host',
        'database',
        'username',
        'password',
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];
}
