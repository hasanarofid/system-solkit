<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'store_name',
        'tagline',
        'about',
        'primary_color',
        'secondary_color',
        'currency',
        'currency_symbol',
        'wa_checkout_template',
        'llm_system_prompt',
        'banner_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_ai_sales_enabled',
    ];

    protected $casts = [
        'is_ai_sales_enabled' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
