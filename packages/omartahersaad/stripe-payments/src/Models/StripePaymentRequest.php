<?php

namespace OmarTaherSaad\StripePayments\Models;

use Illuminate\Database\Eloquent\Model;

class StripePaymentRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_uuid',
        'status',
        'payment_method',
        'checkout_session_id',
        'request_payload',
        'callback_payload',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request_payload' => 'array',
        'callback_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(config('stripe-payments.order_model'), 'order_uuid', 'uuid');
    }
}
