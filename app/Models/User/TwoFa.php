<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\DateTime;

class TwoFa extends Model
{
    use HasFactory, DateTime;

    protected $table = 'two_fas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'mail_limit',
        'expires_at',
    ];

    /**
     * Get the user associated with the TwoFa.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
