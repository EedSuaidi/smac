<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WalletBalance;
use Illuminate\Auth\Access\Response;

class WalletBalancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WalletBalance $walletBalance): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WalletBalance $walletBalance): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WalletBalance $walletBalance): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WalletBalance $walletBalance): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WalletBalance $walletBalance): bool
    {
        return $user->isUser();
    }
}
