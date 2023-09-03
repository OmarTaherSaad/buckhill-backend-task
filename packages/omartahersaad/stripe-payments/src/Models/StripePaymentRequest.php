<?php

namespace OmarTaherSaad\StripePayments\Models;

use Illuminate\Database\Eloquent\Model;

class StripePaymentRequest extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'order_uuid',
        'status',
        'payment_method',
        'checkout_session_id',
        'request_payload',
        'callback_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'callback_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(config('stripe-payments.order_model'), 'order_uuid', 'uuid');
    }
}
