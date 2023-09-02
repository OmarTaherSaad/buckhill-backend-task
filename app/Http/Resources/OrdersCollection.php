<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function (Order $order) {
            return [
                'uuid' => $order->uuid,
                'status' => $order->status,
                'address' => $order->address,
                'delivery_fee' => $order->delivery_fee,
                'amount' => $order->amount,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'shipped_at' => $order->shipped_at,
                'products' => $order->products,
            ];
        })->toArray();
    }
}
