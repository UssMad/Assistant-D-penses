<?php

namespace App\Policies;

use App\Models\Depenses;
use App\Models\User;

class DepensesPolicy
{
    public function view(User $user, Depenses $depense): bool
    {
        return $user->id === $depense->recu->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Depenses $depense): bool
    {
        return $user->id === $depense->recu->user_id;
    }

    public function delete(User $user, Depenses $depense): bool
    {
        return $user->id === $depense->recu->user_id;
    }
}
