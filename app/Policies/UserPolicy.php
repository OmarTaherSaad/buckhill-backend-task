<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_admin || $user->is($model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        return auth()->guest();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        $userUpdate = $user->is($model);
        $adminUpdate = $user->is_admin && !$model->is_admin;
        return $userUpdate || $adminUpdate;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        $userDelete = $user->is($model);
        $adminDelete = $user->is_admin && !$model->is_admin;
        return $userDelete || $adminDelete;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->is_admin;
    }
}
