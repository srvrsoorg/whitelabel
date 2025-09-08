<?php

namespace App\Models\Admin\Webhook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Webhook\Webhook;
use App\Traits\DateTime;

class WebhookEvent extends Model
{
    use HasFactory, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webhook_events';
    protected $hidden = array('pivot');

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type', 
        'action'
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The webhooks that are associated with this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function webhooks()
    {
        return $this->belongsToMany(Webhook::class, 'webhook_webhook_event');
    }
}
