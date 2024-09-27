<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\User;

class UserBillingDetail extends Model
{
    use HasFactory, DateTime;

    protected $table = 'user_billing_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'address',
        'city',
        'region',
        'region_code',
        'country',
        'country_code',
        'postal_code',
        'tax_numbers'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tax_numbers' => 'array',
    ];

    /**
     * Get the user associated with the user billig detail.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
