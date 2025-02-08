<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Server\Server;
use App\Models\Admin\Configuration\Plan;
use App\Models\User\{UsageSummary, CreditReminder};
use App\Traits\DateTime;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'server_id',
        'plan_id',
        'amount',
        'type',
        'monthly_price',
        'created_at',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the user who owns the subscription.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Get the server associated with the subscription.
     */
    public function server(){
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the usageSummaries associated with the subscription.
     */
    public function usageSummaries(){
        return $this->hasMany(UsageSummary::class);
    }

    /**
     * Get the latestUsageSummary associated with the subscription.
     */
    public function latestUsageSummary()
    {
        return $this->hasOne(UsageSummary::class)->latestOfMany();
    }

    /**
     * Get the credit reminder associated with the subscription.
     */
    public function creditReminder(){
        return $this->hasOne(CreditReminder::class);
    }
}