<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class Activity extends Model
{
    use HasFactory, DateTime;

    protected $table = 'activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip',
        'on',
        'action',
        'content',
    ];

    /**
     * Get the user associated with the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
