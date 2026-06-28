<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'email',
        'phone',
        'address',
        'notes',
        'company_name',
        'status',
    ];

      public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
