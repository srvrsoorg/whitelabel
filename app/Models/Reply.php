<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Ticket, User};
use App\Traits\DateTime;

class Reply extends Model
{
    use HasFactory, DateTime;

    protected $table = 'replies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'support_agent_id',
        'reply',
        'attachment',
    ];

    /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the ticket that is associated with the reply.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the support agent that is associated with the reply.
     */
    public function supportAgent()
    {
        return $this->belongsTo(User::class, 'support_agent_id');
    }
}