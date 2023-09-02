<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function (User $user) {
            return [
                'uuid' => $user->uuid,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'is_admin' => $user->is_admin,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'is_marketing' => $user->is_marketing,
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
            ];
        })->toArray();
    }
}
