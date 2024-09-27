<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteSetting extends Model
{
    use HasFactory, SoftDeletes, DateTime;

    protected $table = 'site_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sa_org_id',
        'app_name',
        'tag_line',
        'logo',
        'icon',
        'favicon',
        'color_code',
        'color_palette',
        'header',
        'footer',
        'analytics'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'color_palette' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
