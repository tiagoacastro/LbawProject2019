<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Administrator;
use App\BannedUser;
use App\Purchase;
use App\User;
use App\Game;
use App\Category;

class AdminController extends Controller
{
	use AuthorizesRequests {
        authorize as protected laravelAuthorize;
    }

    public function authorize($ability, $arguments = [])
    {
        if (!Auth::guard('admin')->check()) {
            $this->laravelAuthorize($ability, $arguments);
        }
    }

	public function getSales()
    {
		$sales = DB::table('purchase')
			   ->join('paid', 'purchase.id', '=', 'paid.idpurchase')
			   ->join('game', 'game.id', '=', 'paid.idgame')
			   ->join('paymentmethod', 'paymentmethod.id', '=', 'purchase.idpaymentmethod')
			   ->join('User', 'User.id', '=', 'purchase.iduser')
			   ->orderBy('purchasedate')
			   ->select('name', 'purchasedate', 'value', 'method', 'username')
			   ->take(20)
			   ->get();

    	return $sales;
    }

    public function getUsersQuery($searchText)
    {
    	if($searchText !== "") {
    		$users = DB::select("SELECT 'false' AS banned, name, email, username, path, ts_rank_cd(textsearch, query) AS rank
							FROM NormalUser, \"User\", plainto_tsquery(?) AS query, to_tsvector(username || ' ' || name) AS textsearch, Image
							WHERE query @@ textsearch AND \"User\".profilePicture = Image.id AND \"User\".id = NormalUser.id 
							AND NormalUser.id NOT IN (SELECT id FROM BannedUser)
							UNION
							SELECT 'true' AS banned, name, email, username, path, ts_rank_cd(textsearch, query) AS rank
							FROM NormalUser, \"User\", plainto_tsquery(?) AS query, to_tsvector(username || ' ' || name) AS textsearch, Image
							WHERE query @@ textsearch AND \"User\".profilePicture = Image.id AND \"User\".id = NormalUser.id 
							AND NormalUser.id IN (SELECT id FROM BannedUser)", [$searchText, $searchText]);
    	} else {
    		$banned_users = BannedUser::select('id')->get();
    		$users = DB::table('User')
				   ->join('normaluser', 'User.id', '=', 'normaluser.id')
				   ->join('image', 'image.id', '=', 'User.profilepicture')
				   ->whereNotIn('normaluser.id', $banned_users)
				   ->select(array(DB::raw("'false' AS banned"),'username', 'email', 'name', 'path'))
				   ->take(30)
				   ->get();
    	}

		return $users;
    }

    public function banUser(Request $request, $username) {
		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		$user = User::where('username', $username)->first();
		$banned_user = new BannedUser();
		$banned_user->id = $user->id;
		$banned_user->reason = "unknown";
		$banned_user->save();
		return $user;
	}


    public function getPendingGamesQuery()
    {
    	$games = DB::table('User')
    			->join('selling', 'User.id', '=', 'selling.iduser')
    			->join('game', 'game.id', '=', 'selling.idgame')
    			->join('image', 'game.cover', '=', 'image.id')
    			->where('game.state', '=', 'Pending')
    			->select('image.path', 'game.name', 'game.price', 'User.username', 'game.id')
    			->get();

    	return $games;
    }

    public function getCategoriesQuery()
    {
    	try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

    	$categories = Category::where('name', '!=', "")
    						  ->orderBy('name')
    						  ->get();

    	return $categories;
    }

    public function categoriesAdd()
    {
    	try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$name = request("categoryName");
		if ($name === null)
			return redirect()->route('error');

		Category::insert(['name' => $name]);

		return redirect()->route('admin.categories');
    }

     public function categoriesRemove($id)
    {
    	try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$cat = Category::find($id)->delete();

		return redirect()->route('admin.categories');
    }

 
    public function changeGameState($gameId) {
    
    	try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$value = request('value');
		if ($value === null)
			return redirect()->route('admin.pending_games');

		$game = Game::where('id', $gameId)->first();

		if ($value === "accept")
			$game->state = "Accepted";

		if ($value === "refuse")
			$game->state = "Rejected";

		$game->save();

		return redirect()->route('admin.pending_games');
    }

    public function getUsers()
    {
		$searchText = request('searchText');
		if ($searchText === null)
			$searchText = "";

		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		$users = $this->getUsersQuery($searchText);

		return redirect()->route('admin.search_users')->with(['users' => $users]);
    }


	public function sales() {
		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		
		$sales = $this->getSales();
		return view('pages.admin_sales', ["sales"=>$sales]);
		
	}

	public function pendingGames() {
		
		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$games = $this->getPendingGamesQuery();

		return view('pages.admin_pending_games', ['games' => $games]);
	}

	public function searchUsers() {
		
		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}
		$users = session()->get('users');
			
		if (!isset($users))
			$users = $this->getUsersQuery("");

		return view('pages.admin_search_users', ['users' => $users]);
	}

	public function categories() {
		
		try {
			$this->authorize('sales', Administrator::class);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$categories = $this->getCategoriesQuery();

		return view('pages.admin_categories', ['categories' => $categories]);
	}

	

}
