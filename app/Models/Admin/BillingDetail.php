<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class BillingDetail extends Model
{
    use HasFactory, DateTime;

    protected $table = 'billing_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'address', 
        'city', 
        'region', 
        'region_code', 
        'country', 
        'country_code', 
        'postal_code', 
        'tax_numbers', 
        'billing_mode',
        'new_registration_free_credits',
        'minimum_recharge_amount',
        'due_days',
        'retention_period',
        'currency',
        'currency_symbol'
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tax_numbers' => 'array',
    ];
}
