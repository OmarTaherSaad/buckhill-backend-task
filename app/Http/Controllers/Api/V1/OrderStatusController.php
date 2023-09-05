<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderStatusResource;
use App\Http\Requests\StoreOrderStatusRequest;
use App\Http\Requests\ListOrderStatusesRequest;
use App\Http\Requests\UpdateOrderStatusRequest;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OrderStatus::class, 'orderStatus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListOrderStatusesRequest $request): \Illuminate\Http\JsonResponse
    {
        $orderStatuses = OrderStatus::query()
            ->when($request->has('sortBy'), function ($query) use ($request): void {
                $query->orderBy($request->get('sortBy'), $request->get('desc') ? 'desc' : 'asc');
            })
            ->paginate($request->get('limit', 10));
        return response()->json($orderStatuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderStatusRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $orderStatus = OrderStatus::create($data);
        return (new OrderStatusResource($orderStatus))
            ->additional([
                'success' => true,
                'message' => 'Order status created successfully',
            ])->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $orderStatus): \Illuminate\Http\JsonResponse
    {
        return (new OrderStatusResource($orderStatus))
            ->additional([
                'success' => true,
            ])->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderStatusRequest $request, OrderStatus $orderStatus): \Illuminate\Http\JsonResponse
    {
        $orderStatus->update($request->validated());
        return (new OrderStatusResource($orderStatus))
            ->additional([
                'success' => true,
                'message' => 'Order status updated successfully',
            ])->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $orderStatus): \Illuminate\Http\JsonResponse
    {
        $orderStatus->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order status deleted successfully',
        ]);
    }
}
