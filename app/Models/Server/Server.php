<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Server\{ServerInstance, Subscription, Meta};
use App\Models\Admin\Configuration\{CloudProvider, Plan};
use App\Models\User\{UsageSummary, CreditReminder};
use App\Traits\DateTime;

class Server extends Model
{
    use HasFactory, SoftDeletes ,DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'servers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sa_org_id',
        'sa_server_id',
        'user_id',
        'cloud_provider_id',
        'plan_id',
        'server_instance_id',
        'name',
        'ip',
        'hostname',
        'provider_name',
        'username',
        'password',
        'operating_system',
        'version',
        'arch',
        'web_server',
        'ssh_status',
        'agent_status',
        'agent_version',
        'ssh_port',
        'php_cli_version',
        'key',
        'database_type',
        'database_username',
        'database_password',
        'redis_password',
        'timezone',
        'country_code',
        'nodejs',
        'yarn',
    ];

    /**
     * Get the user who owns the server.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription associated with the server.
     */
    public function subscription(){
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get the cloudProvider associated with the server.
     */
    public function cloudProvider(){
        return $this->belongsTo(CloudProvider::class);
    }

    /**
     * Get the plan associated with the server.
     */
    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the server instance associated with the server.
     */
    public function serverInstance(){
        return $this->belongsTo(ServerInstance::class);
    }

    /**
     * Get the server meta associated with the server.
     */
    public function metas(){
        return $this->hasMany(Meta::class);
    }

    /**
     * Get the usageSummaries associated with the server.
     */
    public function usageSummaries(){
        return $this->hasMany(UsageSummary::class);
    }

    /**
     * Get the credit reminder associated with the server.
     */
    public function creditReminder(){
        return $this->hasOne(CreditReminder::class);
    }

    /**
     * Check the meta value based on the provided name.
     */
    public function getServerCreationMailAttribute(){
        return $this->metas()->where('name', 'server_creation_mail_sent')->exists();
    }

    // Define an accessor for agent_status
    public function getAgentStatusAttribute($value)
    {
        // Replace underscores with spaces
        return str_replace('_', ' ', $value);
    }
}