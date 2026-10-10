<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
//use Illuminate\Auth\Access\Response;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */

    /**public function restore(User $user, Warehouse $warehouse): bool
    {
        return false;
    }
    */

    /**
     * Determine whether the user can permanently delete the model.
     */
    
    /**
     * public function forceDelete(User $user, Warehouse $warehouse): bool
     * {        
        *  return false;    
     * }
    */
}
