<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Game;
use App\Selling;
use App\GameCategory;
use App\Category;
use App\Review;
use App\User;
use App\Image;
use App\Developer;
use App\Vote;

class GameController extends Controller
{
	public function getGame($id){
        $game = Game::where('game.id', $id)
                ->leftJoin('developer', 'developer.id', '=', 'game.iddeveloper')
                ->join('image', 'image.id', '=', 'game.cover')
                ->select('game.id', 'game.name', 'game.briefdescription', 'developer.pen_name', 'game.state', 'game.releasedate',
                 'game.agerestriction', 'game.price', 'game.description', 'game.score', DB::raw('image.path as imagepath'))
                ->firstOrFail();

        $selling = Selling::where('idgame', $id)
                ->join('User', 'User.id', '=', 'selling.iduser')
                ->select('User.id', 'User.username')
                ->firstOrFail();

        $user = User::where('id', $selling->id)->firstOrFail();

        switch($game->state){
            case 'Rejected':
            	return redirect(route('error'));
            case 'Deleted':
            	return redirect(route('error'));
            case 'Pending':
                if (!Auth::guard('admin')->check()) {
                    try {
                        $this->authorize('show', $user);
                    } catch(\Exception $ex) {
                        return redirect('/error');
                    }
                }
                break;
        }

        $genres = GameCategory::where('idgame', $id)
                  ->join('category', 'category.id', '=', 'gamecategory.idcategory')
                  ->select('category.name')
				  ->get();
				  
		if(Auth::check())
			$currentUser = Auth::user()->id;
		else
			$currentUser = -1;

		$reviews = DB::select('SELECT Review.idUser, Review.content, Review.score, Image.path, "User".username, Review.creationDate, Vote.type, Review.votes
								FROM Review JOIN "User" ON "User".id = Review.idUser 
								LEFT JOIN Image ON Image.id = "User".profilePicture
								LEFT JOIN Vote ON Vote.idUserReview = Review.idUser AND Vote.idGame = Review.idGame AND Vote.idUserVote = ?
								WHERE Review.idGame = ? ORDER BY Review.votes DESC, Review.creationDate DESC LIMIT 5', [$currentUser, $id]);

		$auth_user = null;
		if(!Auth::guard('admin')->check() && Auth::check())
			$auth_user = User::where('User.id', Auth::user()->id)
							 ->leftjoin('image', 'profilepicture', '=', 'image.id')
							 ->select('path', 'User.id', 'username')
							 ->first();

		$has_review = 0;
		$has_game = 0;
		$has_favorite = 0;
		if(!Auth::guard('admin')->check() && Auth::check()){
			$has_review = sizeof(Review::where('iduser', $auth_user->id)->where('idgame', $id)->get());
			$has_game = sizeof(DB::table('cart')->where('idgame', $id)->where('iduser', $auth_user->id)->get()) +
    				sizeof(DB::table('paid')->join('purchase', 'idpurchase', '=', 'purchase.id')->where('idgame', $id)->where('iduser', $auth_user->id)->get());
    		$has_favorite = sizeof(DB::table('favorite')->where('idgame', $id)->where('iduser', $auth_user->id)->get());
		}

        return view('pages.game', ['game' => $game, 'creator' => $user, 'genres' => $genres, 'reviews' => $reviews, 'auth_user' => $auth_user,
    								'has_review' => $has_review, 'has_game' => $has_game, 'has_favorite' => $has_favorite]);
    }

    public function loadReviews(Request $request, $id, $loads) {
		$skip = $loads * 5 + 5;
		
		if(Auth::check())
			$currentUser = Auth::user()->id;
		else
			$currentUser = -1;

    	$reviews = DB::select('SELECT Review.idgame, Review.idUser, Review.content, Review.score, Image.path, "User".username, Review.creationDate, Vote.type, Review.votes
								FROM Review JOIN "User" ON "User".id = Review.idUser 
								LEFT JOIN Image ON Image.id = "User".profilePicture
								LEFT JOIN Vote ON Vote.idUserReview = Review.idUser AND Vote.idGame = Review.idGame AND Vote.idUserVote = ?
								WHERE Review.idGame = ? ORDER BY Review.votes DESC, Review.creationDate DESC LIMIT 5 OFFSET ?', [$currentUser, $id, $skip]);

      	return response()->json($reviews);
	}

	public function getSellForm() {
		$categories = Category::all();


		return view('pages.sell_game', ['categories' => $categories]);
	}

	private function validator(array $data) {
		return Validator::make($data, [
			'name' => "required|string|max:64|unique:game",
			'briefdescription' => 'required|string|max:255',
			'image_upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'game_upload' => 'required',
			'gameDeveloper' => 'sometimes|nullable|string|max:32',
			'gamePrice' => 'required|numeric',
			'description' => 'required|string|max:2000'
		]);
	}

	public function addNewGame() {
		$user = Auth::user();
		if (empty($user)) {
			return redirect(route('error'));
		}
		try {
			$this->authorize('show', $user);
		} catch(\Exception $ex) {
			return redirect('/error');
		}

		$validator = $this->validator(request()->all());
		if($validator->fails()) {
			return redirect()->route('games.sell')->withErrors($validator);
		}

		$categories = request('game-genre');
		$age_restriction = request('age-restriction');

		$game = new Game();
		$game->name = request('name');
		$game->description = request('description');
		$game->briefdescription = request('briefdescription');
		$game->price = request('gamePrice');
		$game->score = 0;
		if($age_restriction === "Everyone") {
			$game->agerestriction = 0;
		} else {
			$game->agerestriction = substr($age_restriction, 0, -1);
		}

		if(request('GameDeveloper') !== null) {
			if(sizeof(Developer::where('pen_name', request('gameDeveloper'))->get()) === 0) {
				$developer = new Developer();
				$developer->pen_name = request('gameDeveloper');
				$developer->save();
			} else {
				$developer = Developer::where('pen_name', request('gameDeveloper'))->first();
			}
			$game->iddeveloper = $developer->id;
		}

		$game->path = "/games/dummy.txt";
		$game->cover = 1;
		$game->save();


		$imagePath = '/img/game/game' . $game->id . '.' . request('image_upload')->getClientOriginalExtension();
		request('image_upload')->move(public_path('/img/game/'), 'game' . $game->id . '.' . request('image_upload')->getClientOriginalExtension());
		$new_image = new Image();
		$new_image->path = $imagePath;
		$new_image->save();

		$gamePath = '/games/game' . $game->id . '.' . request('game_upload')->getClientOriginalExtension();
		request('game_upload')->move(public_path('/games/'), 'game' . $game->id . '.' . request('game_upload')->getClientOriginalExtension());
		$game->path = $gamePath;

		$game->cover = $new_image->id;
		$game->save();

		if($categories !== null) {
			foreach($categories as $category) {
				$row_category = Category::where('name', $category)->first();
				DB::table('gamecategory')->insert(
		    		['idgame' => $game->id, 'idcategory' => $row_category->id]
				);
			}
		}

		DB::table('selling')->insert(['iduser'=>$user->id, 'idgame'=>$game->id]);

		return redirect()->route('games', ['id' => $game->id]);
	}

	private function validator_edit(array $data) {
		return Validator::make($data, [
			'name' => "sometimes|nullable|string|max:64|unique:game",
			'briefdescription' => 'sometimes|nullable|string|max:255',
			'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'game_upload' => 'nullable',
			'gameDeveloper' => 'sometimes|nullable|string|max:32',
			'gamePrice' => 'sometimes|nullable|numeric',
			'description' => 'sometimes|nullable|string|max:2000'
		]);
	}

	public function editGame($id) {
		$game = Game::where('id', $id)->first();

		$selling = Selling::where('idgame', $id)->firstOrFail();
        $user = User::where('id', $selling->iduser)->firstOrFail();

        if (!Auth::guard('admin')->check()) {
            try {
                $this->authorize('show', $user);
            } catch(\Exception $ex) {
                return redirect('/error');
            }
        }

        switch($game->state){
            case 'Rejected':
            	return redirect(route('error'));
            case 'Deleted':
            	return redirect(route('error'));
            case 'Pending':
                if (!Auth::guard('admin')->check()) {
                    try {
                        $this->authorize('show', $user);
                    } catch(\Exception $ex) {
                        return redirect('/error');
                    }
                }
                break;
        }

		$categories = Category::all();
		$developer = Developer::where('id', $game->iddeveloper)->first();
		$checked_category = array();

		foreach($categories as $category) {
			if(sizeof(Category::join('gamecategory', 'idcategory', '=', 'id')
						 ->where('id', $category->id)
						 ->where('idgame', $id)->get()) === 0) {
				$checked_category[$category->id] = 0;
			} else {
				$checked_category[$category->id] = 1;
			}
		}

		return view('pages.edit_game', ['categories' => $categories, 'game' => $game, 'checked_category' => $checked_category, 'developer' => $developer, 'creator' => $user]);
	}

	public function updateGame($id) {
		$game = Game::where('id', $id)->first();
		$selling = Selling::where('idgame', $id)->firstOrFail();
        $user = User::where('id', $selling->iduser)->firstOrFail();
        if (!Auth::guard('admin')->check()) {
            try {
                $this->authorize('show', $user);
            } catch(\Exception $ex) {
                return redirect('/error');
            }
        }

		$validator = $this->validator_edit(request()->all());
		if($validator->fails()) {
			return redirect()->route('games.edit', [$id])->withErrors($validator);
		}

		if(request('name') !== null) {
			$game->name = request('name');
		}

		if(request('description') !== null) {
			$game->description = request('description');
		}

		if(request('briefdescription') !== null) {
			$game->briefdescription = request('briefdescription');
		}

		if(request('gamePrice') !== null) {
			$game->price = request('gamePrice');
		}

		if(request('gameDeveloper') !== null) {
			if(sizeof(Developer::where('pen_name', request('gameDeveloper'))->get()) === 0) {
				$developer = new Developer();
				$developer->pen_name = request('gameDeveloper');
				$developer->save();
			} else {
				$developer = Developer::where('pen_name', request('gameDeveloper'))->first();
			}
			$game->iddeveloper = $developer->id;
		}

		if(request('image_upload') !== null) {
			$imagePath = '/img/game/game' . $game->id . '.' . request('image_upload')->getClientOriginalExtension();
			request('image_upload')->move(public_path('/img/game/'), 'game' . $game->id . '.' . request('image_upload')->getClientOriginalExtension());
			$new_image = new Image();
			$new_image->path = $imagePath;
			$new_image->save();
			$game->cover = $new_image->id;
		}

		$game->save();

		
		DB::table('gamecategory')->where('idgame', '=', $id)->delete();

		$categories = request('game-genre');
		
		if($categories !== null) {
			foreach($categories as $category) {
				$row_category = Category::where('name', $category)->first();
				DB::table('gamecategory')->insert(
		    		['idgame' => $game->id, 'idcategory' => $row_category->id]
				);
			}
		}

		$age_restriction = request('age-restriction');
		if($age_restriction === "Everyone") {
			$game->agerestriction = 0;
		} else {
			$game->agerestriction = substr($age_restriction, 0, -1);
		}

		return redirect()->route('games', ['id' => $game->id]);
	}

	public function voteReview(Request $request, $id, $user){
		if(!Auth::check())
			return response()->json(false);

		$data = Vote::where([['idgame','=',$id], ['iduserreview','=', $user], ['iduservote', "=", Auth::user()->id]])
		->select('type')
		->first();

		if($data == null) {
			DB::table('vote')->insert(['idgame' => $id, 'iduserreview' => $user, 'iduservote' => Auth::user()->id, 'type' => $request->input('type')]);
		} else {
			if($data->type == $request->input('type')) {
				DB::table('vote')->where([['idgame','=',$id], ['iduserreview','=', $user], ['iduservote', "=", Auth::user()->id]])->delete();
			} else {
				DB::table('vote')->where([['idgame','=',$id], ['iduserreview','=', $user], ['iduservote', "=", Auth::user()->id]])->delete();
				DB::table('vote')->insert(['idgame' => $id, 'iduserreview' => $user, 'iduservote' => Auth::user()->id, 'type' => $request->input('type')]);
			}
		}
		
        return response()->json(true);
	}

	public function deleteGame($id) {
		$game = Game::where('id', $id)->first();
		$selling = Selling::where('idgame', $id)->firstOrFail();
        $user = User::where('id', $selling->iduser)->firstOrFail();
        if (!Auth::guard('admin')->check()) {
            try {
                $this->authorize('show', $user);
            } catch(\Exception $ex) {
                return redirect('/error');
            }
        }

        $game = Game::where('id', $id)->firstOrFail();
		$game->state = "Deleted";
		$game->save();

		return redirect()->route('homepage');
	}

	public function addReview($id) {
		$content = request('content');
		$score = request('score');

		DB::table('review')->insert(
    		['idgame' => $id, 'content' => $content, 'score' => $score, 'iduser' => Auth::user()->id]
		);

		return redirect()->route('games', ['id' => $id]);
	}

	public function editReview($id) {
		$content = request('content');
		$score = request('score');

		$review = DB::table('review')->where('iduser', Auth::user()->id)->update(['content' => $content, 'score' => $score]);

		return redirect()->route('games', ['id' => $id]);
	}

	public function deleteReview($id) {
		$content = request('content');
		$score = request('score');

		$review = DB::table('review')->where('iduser', Auth::user()->id)->delete();

		return redirect()->route('games', ['id' => $id]);
	}
}