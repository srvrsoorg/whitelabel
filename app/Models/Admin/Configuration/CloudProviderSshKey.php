<?php

namespace App\Models\Admin\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Admin\Configuration\CloudProvider;

class CloudProviderSshKey extends Model
{
    use HasFactory, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cloud_provider_ssh_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'name',
        'ssh_key_id',
        'region',
        'key'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the cloud provider that owns the SSH key.
     */
    public function cloudProvider()
    {
        return $this->belongsTo(CloudProvider::class);
    }
}