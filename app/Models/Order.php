<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'products',
        'address',
        'delivery_fee',
        'amount',
        'shipped_at',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
        'shipped_at' => 'datetime',
    ];

    #region Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    #endregion

    #region Mutators

    public function getStatusAttribute()
    {
        return $this->orderStatus?->title;
    }

    #endregion


}
