<?php

namespace App\Enums;

enum PaymentType: string
{
    case Credit_Card = 'credit_card';
    case Cash_On_Delivery = 'cash_on_delivery';
    case Bank_Transfer = 'bank_transfer';
}
