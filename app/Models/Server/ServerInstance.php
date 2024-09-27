<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Admin\Configuration\CloudProvider;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerInstance extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    // Operations table
    protected $table = 'server_instances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cloud_provider_id',
        'instance_id',
        'name',
        'ip',
        'version',
        'key',
        'price',
        'cpu',
        'disk_size',
        'memory',
        'transfer',
        'region',
        'region_zone',
        'status',
        'deleted_at'
    ];

     /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the cloud provider associated with the server instance.
     */
    public function cloudProvider()
    {
        return $this->belongsTo(CloudProvider::class);
    }

    /**
     * Get the server associated with the server instance.
     */
    public function server()
    {
        return $this->hasOne(Server::class);
    }
}