<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Billing\Transaction;
use App\Traits\DateTime;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promo_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'code',
        'description',
        'discount',
        'usage',
        'usable',
        'customer_type',
        'expires_at',
    ];

   protected $casts = ['expires_at' => 'datetime:Y-m-d H-i-s', 'discount' => 'float'];

    /**
     * Get the transactions associated with the promo code.
     */
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
