<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    // Only the owner of the profile can see its profile.
    public function show(User $user, User $user2)
    {
      return $user->id === $user2->id;
    }
}
