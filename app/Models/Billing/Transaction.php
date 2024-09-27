<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Billing\PromoCode;
use App\Traits\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaction extends Model
{
    use HasFactory, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'promo_code_id',
        'payment_gateway',
        'service',
        'base_amount',
        'discount_amount',
        'tax_amount',
        'final_amount',
        'status',
        'key',
        'transaction_id',
        'payment_link',
        'company_address',
        'company_tax_numbers',
        'tax_details',
        'address',
        'tax_numbers',
        'refund_id',
        'refund_reason',
        'refunded_at',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'company_address' => 'array',
        'address' => 'array', 
        'company_tax_numbers' => 'array',
        'tax_numbers' => 'array',
        'tax_details' => 'array',
    ];

    /**
     * Get the user associated with the transaction.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Get the promo code used in the transaction.
     */
    public function promoCode(){
        return $this->belongsTo(PromoCode::class);
    }

    /**
     * Get the paid_at based on timezone.
     */
    protected function paidAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone(optional($this->user)->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s')
        );
    }
}
