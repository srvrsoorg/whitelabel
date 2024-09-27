<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class Login extends Model
{
    use HasFactory, DateTime;

    protected $table = 'logins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip',
        'browser_agent',
    ];

    /**
     * Get the user associated with the login.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
