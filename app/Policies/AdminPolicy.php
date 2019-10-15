<?php

namespace App\Policies;

use App\Administrator;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class AdminPolicy
{
    use HandlesAuthorization;

    public function sales() {
		return Auth::guard('admin')->check();
    }
}