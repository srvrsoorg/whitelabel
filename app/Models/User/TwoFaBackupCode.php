<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\DateTime;

class TwoFaBackupCode extends Model
{
    use HasFactory, DateTime;

    protected $table = 'two_fa_backup_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'backup_code',
        'used',
        'created_at'
    ];

    /**
     * Get the user associated with the TwoFaBackupCode.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
