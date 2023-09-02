<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListUserOrdersRequest;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\OrdersCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersCollection;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListUsersRequest $request)
    {
        $data = $request->validated();
        $users = User::query();

        //Rename 'phone' to 'phone_number' if exists
        if (isset($data['phone'])) {
            $data['phone_number'] = $data['phone'];
            unset($data['phone']);
        }
        //Do filters for string fields
        $stringFilterKeys = ['first_name', 'last_name', 'email', 'phone_number', 'address'];
        foreach ($stringFilterKeys as $key) {
            if (isset($data[$key])) {
                // Make a case-insensitive search
                $users = $users->whereRaw("LOWER({$key}) LIKE '%" . strtolower($data[$key]) . "%'");
            }
        }
        // Filter by marketing flag
        if (isset($data['marketing'])) {
            $users->where('is_marketing', $data['marketing']);
        }
        // Filter by creation date
        if (isset($data['created_at'])) {
            $users->whereDate('created_at', $data['created_at']);
        }
        // Sort
        if (isset($data['sortBy'])) {
            $users->orderBy($data['sortBy'], $data['desc'] ? 'desc' : 'asc');
        }
        // Paginate
        $users = $users->paginate($data['limit'] ?? 10);
        return (new UsersCollection($users))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        // Add UUID to the data
        $data['uuid'] = Str::uuid()->toString();
        // Hash the password
        $data['password'] = bcrypt($data['password']);
        //Add admin flag
        $data['is_admin'] = true;
        //Rename marketing flag
        $data['is_marketing'] = $data['marketing'] ?? false;
        // Create the admin user
        User::create($data);
        return response()->json([
            'success'   => true,
            'message'   => 'Admin created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        // Hash the password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        // Update the user
        $user->update($data);
        // Return the updated user
        return response()->json([
            'success'   => true,
            'message'   => 'User updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->deleteRelated();
        $user->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'User deleted successfully',
        ]);
    }
}
