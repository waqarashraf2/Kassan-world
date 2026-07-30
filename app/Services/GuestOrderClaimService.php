<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class GuestOrderClaimService
{
    public function claimFor(User $user): int
    {
        if (! $user->email) {
            return 0;
        }

        return Order::query()
            ->whereNull('user_id')
            ->where('customer_email', $user->email)
            ->update(['user_id' => $user->id]);
    }
}
