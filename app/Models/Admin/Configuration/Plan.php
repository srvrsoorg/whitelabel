<?php

namespace App\Models\Admin\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Admin\Configuration\CloudProvider;
use App\Models\Server\Subscription;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'name',
        'region',
        'region_code',
        'type',
        'cores',
        'ram',
        'disk',
        'bandwidth',
        'server_price',
        'price_per_month',
        'size_slug',
        'visible',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the cloud provider that owns the plan.
     */
    public function cloudProvider()
    {
        return $this->belongsTo(CloudProvider::class, 'provider_id', 'id');
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}