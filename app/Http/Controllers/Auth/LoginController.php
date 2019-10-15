<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\BannedUser;
use App\User;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as doLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function attemptLogin(Request $request)
    {

        if (Auth::guard('admin')->attempt(['username' => request('username'), 'password' => request('password')])){
            return redirect()->intended('homepage');
        } else {
            $banned = 0;
            $user = User::where('username', request('username'))->first();
            if(!empty($user)) {
                $banned_user = BannedUser::where('id', $user->id)->first();
                if(!empty($banned_user))
                    $banned = 1;
            }
            if ($banned === 0){
                if(Auth::attempt(['username' => request('username'), 'password' => request('password')])) {
                    return redirect()->intended('homepage');
                }
            }  else if($banned === 1) {
                throw ValidationException::withMessages([
                    'banned' => [trans('auth.banned')],
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $this->doLogout($request);

        return redirect()->route('login');
    }
}
