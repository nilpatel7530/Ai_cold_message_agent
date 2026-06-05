<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'company_name',
        'website_url',
        'phone_number',
        'status',
        'raw_research_data',
        'compressed_context',
        'generated_copy',
        'error_logs',
        'attempts',
    ];
}
