<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'uuid',
        'title',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
