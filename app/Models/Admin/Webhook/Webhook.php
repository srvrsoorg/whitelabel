<?php

namespace App\Models\Admin\Webhook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Webhook\WebhookLog;
use App\Models\Admin\Webhook\WebhookEvent;
use App\Traits\DateTime;

class Webhook extends Model
{
    use HasFactory, DateTime;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webhooks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'url', 
        'status'
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The events that belong to the webhook.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany(WebhookEvent::class, 'webhook_webhook_event');
    }

     /**
     * The logs associated with the webhook.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(WebhookLog::class);
    }
}
