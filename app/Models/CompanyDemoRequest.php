<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'contact_name',
    'email',
    'phone',
    'message',
    'locale',
    'ip_address',
])]
class CompanyDemoRequest extends Model
{
    //
}
