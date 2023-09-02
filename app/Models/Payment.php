<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'type',
        'details',
    ];

    protected $casts = [
        'type' => PaymentType::class,
        'details' => 'array',
    ];

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
