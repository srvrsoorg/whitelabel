<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\DateTime;
use App\Models\User;
use App\Models\Server\{Server, Subscription};

class UsageSummary extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'server_id',
        'subscription_id',
        'server_ip',
        'server_name',
        'deduct_amount',
        'started_at',
        'last_deduct_at'
    ];

    /**
     * Get the user that owns the usage summary.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the server associated with the usage summary.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the subscription associated with the usage summary.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}