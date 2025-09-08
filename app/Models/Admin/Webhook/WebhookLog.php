<?php

namespace App\Models\Admin\Webhook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Webhook\Webhook;
use App\Models\Admin\Webhook\WebhookEvent;
use Carbon\Carbon;
use App\Traits\DateTime;

class WebhookLog extends Model
{
    use HasFactory, DateTime;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webhook_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'webhook_id', 
        'webhook_event_id',
        'payload',
        'response_code',
        'response_body',
        'status',
        'delivered_at'
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'delivered_at' => 'datetime',
    ];

    // Accessor for the `delivered_at` attribute.
    public function getDeliveredAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y h:iA') : null;
    }

    /**
     * Get the webhook associated with this log entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * Get the event associated with this log entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(WebhookEvent::class);
    }
}
