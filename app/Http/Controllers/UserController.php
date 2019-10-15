<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\User;
use App\NormalUser;
use App\Image;
use App\Purchase;

class UserController extends Controller
{
	public function getProfile($username) {
		$user = User::where('username', $username)->first();
		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('error'));
		}

		if (!Auth::guard('admin')->check()) {
			try {
				$this->authorize('show', $user);
			} catch(\Exception $ex) {
				return redirect('/error');
			}
		}
		
		$normal_user = NormalUser::where('id', $user->id)->first();

		$purchases = $user->purchases()
						  ->join('paid', 'purchase.id', '=', 'paid.idpurchase')
						  ->join('game', 'game.id', '=', 'paid.idgame')
						  ->select('name', 'purchasedate', 'value')
						  ->get();


		$image = Image::where('id', $user->profilepicture)->first();
		if($image === null){
			$image = Image::first();
			$image->path = "/img/avatar.png";
		}

		return view('pages.profile', ["user"=>$user, "normal_user"=>$normal_user, "image"=>$image, "purchases"=>$purchases]);
	}

	public function editProfile($username) {
		$user = User::where('username', $username)->first();
		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('error'));
		}
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		$normal_user = NormalUser::where('id', $user->id)->first();

		$image = Image::where('id', $user->profilepicture)->first();
		if($image === null){
			$image = Image::first();
			$image->path = "/img/avatar.png";
		}

		return view('pages.edit_profile', ["user"=>$user, "normal_user"=>$normal_user, "image"=>$image]);
	}
	
	private function validator(array $data) {
		return Validator::make($data, [
			'name' => "sometimes|nullable|string|max:255",
			'email' => 'sometimes|nullable|string|email|max:255|unique:User',
			'password' => 'sometimes|nullable|string|min:6|confirmed',
			'file_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
		]);
	}

	public function updateProfile($username) {

		$user = User::where('username', $username)->first();
		if (empty($user)) {
			return redirect(route('error'));
		}
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		$normal_user = NormalUser::where('id', $user->id)->first();
		$validator = $this->validator(request()->all());
		if($validator->fails()) {
			return redirect()->route('users.edit', ['username' => $username])->withErrors($validator);
		}
		if(request('password') !== null) {
			$user->password=bcrypt(request('password'));
			$user->save();
		}
		if(request('file_upload') !== null) { 
			$imagePath = '/img/users/user' . $user->id . '.' . request('file_upload')->getClientOriginalExtension();
			request('file_upload')->move(public_path('/img/users/'), 'user' . $user->id . '.' . request('file_upload')->getClientOriginalExtension());
			$new_image = new Image();
			$new_image->path = $imagePath;
			$new_image->save();

			$user->profilepicture = $new_image->id;
			$user->save();
		}

		if(request('name') !== null) { 
			$normal_user->name = request('name');
			$normal_user->save();
		}

		if(request('email') !== null) { 
			$user->email = request('email');
			$user->save();
		}

		$purchases = $user->purchases()
						  ->join('paid', 'purchase.id', '=', 'paid.idpurchase')
						  ->join('game', 'game.id', '=', 'paid.idgame')
						  ->select('name', 'purchasedate', 'value')
						  ->get();


		$image = Image::where('id', $user->profilepicture)->first();
		if($image === null){
			$image = Image::first();
			$image->path = "/img/avatar.png";
		}
		return redirect()->route('users', ['username' => $username]);
	}

	public function deleteUser($username){
		$user = User::where('username', $username)->first();
		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('error'));
		}
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		
		$normal_user = NormalUser::where('id', $user->id)->firstOrFail();
		$normal_user->delete();

		return redirect()->route('logout');
	}
}
