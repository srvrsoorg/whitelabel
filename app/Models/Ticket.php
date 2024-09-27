<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User, Reply};
use App\Models\Server\Server;
use App\Traits\DateTime;

class Ticket extends Model
{
    use HasFactory, DateTime;

    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'server_id',
        'title',
        'department',
        'status',
    ];

    /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * Get the user that owns the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the server associated with the ticket.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the replies for the ticket.
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the latest reply for the ticket.
     */
    public function latestReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }
}