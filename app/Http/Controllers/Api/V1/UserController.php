<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        // Add UUID to the data
        $data['uuid'] = Str::uuid()->toString();
        // Hash the password
        $data['password'] = bcrypt($data['password']);
        // Create the user
        $user = User::create($data);
        // Issue the token and return it
        return response()->json([
            'success'   => true,
            'message'   => 'User created successfully',
            'token'     => issueToken($user),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function showSelf()
    {
        $user = auth()->user();
        $this->authorize('view', $user);
        return (new UserResource($user))->response()->setStatusCode(200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
