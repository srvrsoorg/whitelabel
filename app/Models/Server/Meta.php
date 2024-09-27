<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Server\Server;
use App\Traits\DateTime;

class Meta extends Model
{
    use HasFactory, DateTime;

    // Operations table
    protected $table = 'server_metas';

    // Timestamps off
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'server_id',
        'name',
        'value'
    ];

    /**
     * Get the server associated with the meta.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
