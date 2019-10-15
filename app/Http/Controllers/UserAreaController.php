<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use App\User;
use App\Cart;
use App\Game;
use App\Purchase;

class UserAreaController extends Controller
{
    public function getPurchaseHistory($username) {
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

		$purchases = $user->purchases()
						  ->join('paid', 'purchase.id', '=', 'paid.idpurchase')
						  ->join('paymentmethod', 'purchase.idpaymentmethod', '=', 'paymentmethod.id')
						  ->join('game', 'game.id', '=', 'paid.idgame')
						  ->select('name', 'purchasedate', 'value', 'nif', 'method')
						  ->get();

		return view('pages.purchases', ["purchases"=>$purchases]);
		
    }

    public function getFavorites($username) {
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

		$wish_list = DB::select('SELECT Image.path, Game.id, Game.name, Game.price
								 FROM Image, Game, Favorite
								 WHERE Favorite.idUser = ? AND Favorite.idGame = Game.id AND Game.cover = Image.id
								 AND Favorite.idUser NOT IN 
								 (SELECT Purchase.idUser FROM Paid, Purchase WHERE Paid.idGame = Game.id 
								 AND Purchase.id = Paid.idPurchase)
								 AND Favorite.idUser NOT IN 
								 (SELECT Cart.idUser FROM Cart WHERE Cart.idGame = Game.id)', [$user->id]);

		$wish_list_cart = DB::select('SELECT Image.path, Game.id, Game.name, Game.price
								 FROM Image, Game, Favorite
								 WHERE Favorite.idUser = ? AND Favorite.idGame = Game.id AND Game.cover = Image.id
								 AND Favorite.idUser NOT IN 
								 (SELECT Purchase.idUser FROM Paid, Purchase WHERE Paid.idGame = Game.id 
								 AND Purchase.id = Paid.idPurchase)
								 AND Favorite.idUser IN 
								 (SELECT Cart.idUser FROM Cart WHERE Cart.idGame = Game.id)', [$user->id]);

		$favorites = DB::select('SELECT Image.path, Game.id, Game.name, Game.price
								 FROM Image, Game, Favorite
								 WHERE Favorite.idUser = ? AND Favorite.idGame = Game.id AND Game.cover = Image.id
								 AND Favorite.idUser IN 
								 (SELECT Purchase.idUser FROM Paid, Purchase WHERE Paid.idGame = Game.id 
								 AND Purchase.id = Paid.idPurchase)', [$user->id]);

		return view('pages.favorites', ["wish_list"=>$wish_list, "wish_list_cart"=>$wish_list_cart, "favorites"=>$favorites]);
    }

    public function addToFavorites(Request $request, $username, $game) {
    	$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		
		DB::table('favorite')->insert(
    		['iduser' => $user->id, 'idgame' => $game]
		);

      	return $game;
	}

    public function removeFromFavorites(Request $request, $username, $game) {
    	$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		DB::table('favorite')->where('iduser', '=', $user->id)->where('idgame', '=', $game)->delete();

      	return $game;
	}

    public function getLibrary($username) {
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

		$games = DB::select('SELECT Image.path AS image_path, Game.id, Game.name, Game.path AS game_path, Purchase.purchaseDate
							 FROM Image, Game, Paid, Purchase
							 WHERE Purchase.idUser = ? AND Paid.idGame = Game.id AND Game.cover = Image.id
							 AND Paid.idGame = Game.id AND Purchase.id = Paid.idPurchase', [$user->id]);

		return view('pages.library', ["games"=>$games]);
    }

    public function downloadGame($username, $game_id) {
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

		$game = Game::where('id', $game_id)->first();
		return response()->download(public_path($game->path));
    }

    public function getCart($username) {
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

		$games = DB::select('SELECT Image.path, Game.id, Game.name, Game.price
							 FROM Image, Game, Cart
							 WHERE Cart.idUser = ? AND Cart.idGame = Game.id AND Game.cover = Image.id',
							 [$user->id]);
		$sum_prices = 0;
		foreach($games as $game) {
			$sum_prices += $game->price;
		}

		return view('pages.cart', ["games"=>$games, "sum_prices"=>$sum_prices]);
    }

    public function addToCart(Request $request, $username, $game) {
    	$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		
		DB::table('cart')->insert(
    		['iduser' => $user->id, 'idgame' => $game]
		);

      	return $game;
	}

	public function removeFromCart(Request $request, $username, $game) {
    	$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		DB::table('cart')->where('iduser', '=', $user->id)->where('idgame', '=', $game)->delete();
		$games = DB::select('SELECT Game.price
							 FROM Game, Cart
							 WHERE Cart.idUser = ? AND Cart.idGame = Game.id',
							 [$user->id]);
		$sum_prices = 0;
		foreach($games as $single_game) {
			$sum_prices += $single_game->price;
		}

      	return ['game' => $game, 'sum_prices' => $sum_prices];
	}

	public function checkoutCart($username) {
		$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$nif = request('nif');

		DB::beginTransaction();

		$purchase = new Purchase();
		$purchase->iduser = $user->id;
		$purchase->totalpaid = 0;
		$purchase->nif = $nif;
		$purchase->idpaymentmethod = 1;
		$purchase->save();

		DB::select("SELECT InsertPaidGames(?, ?)", [$purchase->id, $user->id]);

		DB::commit();

		return redirect()->route('users.library', ['username' => $username]);
	}

    public function getGamesForSale($username) {
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

		$games = DB::select('SELECT Image.path, Game.id, Game.name, Game.price, Game.state, Selling.releaseDate
							 FROM Image, Game, Selling
							 WHERE Selling.idUser = ? AND Selling.idGame = Game.id AND Game.cover = Image.id
							 ORDER BY state DESC',
							 [$user->id]);
		
		return view('pages.games_for_sale', ["games"=>$games]);
    }

    public function deleteGameRequest(Request $request, $username, $game) {
    	$user = User::where('username', $username)->first();
    	
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		DB::table('selling')->where('iduser', '=', $user->id)->where('idgame', '=', $game)->delete();
		
      	return $game;
	}

}
