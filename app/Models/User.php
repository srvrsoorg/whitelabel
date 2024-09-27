<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\{DateTime, CustomBillable};
use App\Models\User\{UserMeta, Login, TwoFa, TwoFaBackupCode, Activity, Payment, UserBillingDetail, UsageSummary, CreditReminder};
use App\Models\Admin\AdminActivity;
use App\Models\Billing\Transaction;
use App\Models\Server\{Server, Subscription};
use App\Models\Ticket;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, DateTime, CustomBillable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'mobile_no',
        'role',
        'status',
        'avatar',
        'credits',
        'country_name',
        'country_code',
        'region_name',
        'region_code',
        'api_token',
        'google2fa_enable',
        'google2fa_secret',
        'timezone',
        'two_fa_enable',
        'stripe_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'role',
        'api_token',
        'password',
        'google2fa_secret',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'google2fa_enable' => 'boolean',
        'two_fa_enable' => 'boolean',
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Check if the user is an administrator
    public function isSuperAdmin()
    {
        return $this->role == 'administrator';
    }

    /**
     * Get the metas associated with the user.
     */
    public function metas()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * Get the logins associated with the user.
     */
    public function logins()
    {
        return $this->hasMany(Login::class);
    }

    /**
     * Store the logins history.
     */
    public function storeLogin()
    {
        $this->logins()->create(['ip'=> request()->ip(), 'browser_agent'=> request()->userAgent()])->refresh();
    }
    
    /**
     * Get the twofa associated with the user.
     */
    public function twoFa()
    {
        return $this->hasOne(TwoFa::class);
    }

    /**
     * Get the backupCodes associated with the user.
     */
    public function backupCodes()
    {
        return $this->hasMany(TwoFaBackupCode::class);
    }

    /**
     * Get the admin activities associated with the user.
     */
    public function adminActivities()
    {
        return $this->hasMany(AdminActivity::class);
    }

    /**
     * Get the activities associated with the user.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the servers associated with the user.
     */
    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    /**
     * Get the transactions associated with the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the subscriptions associated with the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the billing details associated with the user.
     */
    public function billingDetail()
    {
        return $this->hasOne(UserBillingDetail::class);
    }

    /**
     * Define a hasOne relationship with the UserMeta model to retrieve the user latest ban lock reason.
     */
    public function banLockReason()
    {
        return $this->hasOne(UserMeta::class)->where('name', 'account_reason')->latestOfMany();
    }

    /**
     * Get the tickets associated with the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the usageSummaries associated with the user.
     */
    public function usageSummaries()
    {
        return $this->hasMany(UsageSummary::class);
    }

    /**
     * Get the credit reminders associated with the user.
     */
    public function creditReminders()
    {
        return $this->hasMany(CreditReminder::class);
    }

    /**
     * Get the region code based on the billing details.
     */
    public function getRegionCodeValue()
    {
        if($this->billingDetail && $this->billingDetail->region_code) {
            return $this->billingDetail->region_code;
        } else {
            return $this->region_code;
        }
    }

    /**
     * Get the country code based on the billing details.
     */
    public function getCountryCodeValue()
    {
        if($this->billingDetail && $this->billingDetail->country_code) {
            return $this->billingDetail->country_code;
        } else {
            return $this->country_code;
        }
    }

    /**
     * Get the region based on the billing details.
     */
    public function getRegionNameValue()
    {
        if($this->billingDetail && $this->billingDetail->region) {
            return $this->billingDetail->region;
        } else {
            return $this->region_name;
        }
    }
}