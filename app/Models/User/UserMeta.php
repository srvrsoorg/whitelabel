<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\User;

class UserMeta extends Model
{
    use HasFactory, DateTime;

    protected $table = 'user_metas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'value',
    ];

    /**
     * Get the user that owns the meta data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}