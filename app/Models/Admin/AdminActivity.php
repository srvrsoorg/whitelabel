<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\DateTime;

class AdminActivity extends Model
{
    use HasFactory, DateTime;

    protected $table = 'admin_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'on',
        'action',
        'content',
        'ip',
    ];

    /**
     * Get the user associated with the admin activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
