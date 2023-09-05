<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\OrdersCollection;
use App\Http\Requests\ListUserOrdersRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        // Hash the password
        $data['password'] = bcrypt($data['password']);
        // Create the user
        User::create($data);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSelf()
    {
        $user = auth()->user();
        $this->authorize('view', $user);
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSelf(UpdateUserRequest $request)
    {
        $data = $request->validated();
        // Hash the password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        // Update the user
        auth()->user()->update($data);
        // Return the updated user
        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySelf()
    {
        $user = auth()->user();
        $user->deleteRelated();
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Display listing of user orders
     * @param ListUserOrdersRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(ListUserOrdersRequest $request)
    {
        $user = auth()->user();
        $orders = $user->orders();
        if ($request->has('sortBy')) {
            $orders = $orders->orderBy($request->input('sortBy'), $request->input('desc') ? 'desc' : 'asc');
        }
        $orders = $orders->paginate($request->input('limit', 10));
        return (new OrdersCollection($orders))
            ->additional([
                'success' => true,
                'message' => 'User orders retrieved successfully',
            ])
            ->response();
    }
}
