<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Server\{Server, Subscription};
use App\Traits\DateTime;

class CreditReminder extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    protected $table = 'credit_reminders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'server_id',
        'subscription_id',
        'started_at',
        'due_at',
        'end_at',
        'sent_date',
    ];

    /**
     * Get the user associated with the credit reminder.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the server associated with the credit reminder.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the subscription associated with the credit reminder.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
