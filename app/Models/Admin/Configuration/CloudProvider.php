<?php

namespace App\Models\Admin\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Admin\Configuration\{CloudProviderSshKey, Plan};
use App\Models\Server\{ServerInstance, Server};
use Illuminate\Database\Eloquent\SoftDeletes;

class CloudProvider extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cloud_providers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider',
        'access_key',
        'access_secret',
        'visible',
        'key',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the SSH keys associated with the cloud provider.
     */
    public function sshKeys()
    {
        return $this->hasMany(CloudProviderSshKey::class, 'provider_id', 'id');
    }

    /**
     * Get the plans associated with the cloud provider.
     */
    public function plans()
    {
        return $this->hasMany(Plan::class, 'provider_id', 'id');
    }

    /**
     * Get the server instances associated with the cloud provider.
     */
    public function serverInstances()
    {
        return $this->hasMany(ServerInstance::class);
    }

    /**
     * Get the servers associated with the cloud provider.
     */
    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}