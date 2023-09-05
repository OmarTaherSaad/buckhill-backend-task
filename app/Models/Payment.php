<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    use HasUuid;

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
