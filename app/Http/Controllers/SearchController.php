<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Category;
use App\Game;

class SearchController extends Controller
{
    public function getHomepage() {
    	$categories = Category::all();

    	$top_2_popular_games = Game::join('image', 'image.id', '=', 'game.cover')
    					    		->where('game.state', 'Accepted')
    	    						->select('game.id', 'image.path', 'name', 'price', 'score')
    								->orderBy('score', 'desc')->take(2)->get();
    	$top_5_popular_games = Game::join('image', 'image.id', '=', 'game.cover')
    					    		->where('game.state', 'Accepted')
    	    						->select('game.id', 'image.path', 'name', 'price', 'score')
    								->orderBy('score', 'desc')->skip(2)->take(3)->get();


    	$top_2_recent_games = Game::join('image', 'image.id', '=', 'game.cover')
    					    		->where('game.state', 'Accepted')
    	    						->select('game.id', 'image.path', 'name', 'price', 'score')
    								->orderBy('releasedate', 'desc')->take(2)->get();
    	$top_5_recent_games = Game::join('image', 'image.id', '=', 'game.cover')
    					    		->where('game.state', 'Accepted')
    	    						->select('game.id', 'image.path', 'name', 'price', 'score')
    								->orderBy('releasedate', 'desc')->skip(2)->take(3)->get();

        $user_top_2_popular_games_owns = null;
        foreach($top_2_popular_games as $game) {
            $aux = $this->checkUserOwnsGame($game);
            if($aux !== null)
                $user_top_2_popular_games_owns[$game->id] = $aux;
        }
        $user_top_5_popular_games_owns = null;
        foreach($top_5_popular_games as $game) {
            $aux = $this->checkUserOwnsGame($game);
            if($aux !== null)
                $user_top_5_popular_games_owns[$game->id] = $aux;
        }
        $user_top_2_recent_games_owns = null;
        foreach($top_2_recent_games as $game) {
            $aux = $this->checkUserOwnsGame($game);
            if($aux !== null)
                $user_top_2_recent_games_owns[$game->id] = $aux;
        }
        $user_top_5_recent_games_owns = null;
        foreach($top_5_recent_games as $game) {
            $aux = $this->checkUserOwnsGame($game);
            if($aux !== null)
                $user_top_5_recent_games_owns[$game->id] = $aux;
        }

        return view('pages.homepage', ['categories' => $categories, 'pop2games' => $top_2_popular_games, 'pop3games' => $top_5_popular_games, 'rec2games' => $top_2_recent_games, 'rec3games' => $top_5_recent_games, 'pop2games_owns' => $user_top_2_popular_games_owns, 'pop3games_owns' => $user_top_5_popular_games_owns, 
            'rec2games_owns' => $user_top_2_recent_games_owns, 'rec3games_owns' => $user_top_5_recent_games_owns]);
    }

    private function checkUserOwnsGame($game) {
        $aux = 0;
        if(Auth::check()) {
            if(sizeof(DB::table('cart')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) !== 0 ||
                sizeof(DB::table('paid')->join('purchase', 'idpurchase', '=', 'purchase.id')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) !== 0
                || sizeof(DB::table('selling')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) !== 0) {
                $aux = 1;
            } 
            if(sizeof(DB::table('favorite')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) !== 0) {
                if($aux === 0)
                    $aux = 2;
                else $aux = 3;
            }
            return $aux; 
        }
        return 3;
    }

    public function getGamesForSearchPage(Request $request)
    {
    	$category = $request->input('category');
		$keywords = $request->input('keywords');
		$order = $request->input('order');
		if($order === null)
			$order = 'score';
    	$games = null;

    	if($category === null && $keywords === null) {
    		$games = Game::join('image', 'image.id', '=', 'game.cover')
    					    ->where('game.state', 'Accepted')
    	    				->select('game.id', 'image.path', 'name', 'price', 'score', 'releasedate')
    						->orderBy($order, 'desc')->take(20)->get();
    	} else if($category !== null && $keywords === null){
    		$games = Game::join('image', 'image.id', '=', 'game.cover')
    					    ->join('gamecategory', 'idgame', '=', 'game.id')
    					    ->join('category', 'idcategory', '=', 'category.id')
    					    ->where('category.name', $category)
    					    ->where('game.state', 'Accepted')
    	    				->select('game.id', 'image.path', 'game.name', 'price', 'score', 'releasedate')
    						->orderBy($order, 'desc')->take(20)->get();
    	} else if($category === null && $keywords !== null){
    		$games = DB::select("SELECT DISTINCT Game.id, Game.name, Game.price, Game.score, Image.path, Game.releaseDate, ts_rank_cd(textsearch, query) AS ranking
								FROM Game, plainto_tsquery(?) AS query, to_tsvector(name || ' ' || description) AS textsearch, Image
								WHERE query @@ textsearch AND Game.cover = Image.id AND Game.state = 'Accepted' LIMIT 20", [$keywords]);
			$func = function($a, $b) use ($order){
				switch($order){
					case 'score':
						return $a->score < $b->score;
					case 'price':
						return $a->price < $b->price;
					case 'releaseDate':
						return strtotime($a->releasedate) < strtotime($b->releasedate);
				}
				return $a->score < $b->score;
			};
			usort($games, $func);
    	} else if($category !== null && $keywords !== null){
    		$games = DB::select("SELECT DISTINCT Game.id, Game.name, Game.price, Game.score, Image.path, Game.releaseDate, ts_rank_cd(textsearch, query) AS ranking
								FROM Game, plainto_tsquery(?) AS query, to_tsvector(name || ' ' || description) AS textsearch, Image, Category, GameCategory
								WHERE query @@ textsearch AND Game.cover = Image.id AND GameCategory.idGame = Game.id AND Game.state = 'Accepted'
								AND GameCategory.idCategory = Category.id AND Category.name = ? ORDER BY score DESC", [$keywords, $category]);
			$func = function($a, $b) use ($order){
				switch($order){
					case 'score':
						return $a->score < $b->score;
					case 'price':
						return $a->price < $b->price;
					case 'releaseDate':
						return strtotime($a->releasedate) < strtotime($b->releasedate);
				}
				return $a->score < $b->score;
			};
			usort($games, $func);
		}

		return $games;
	}

    public function getSearchPage(Request $request) {
    	$categories = Category::all();

    	$games = $this->getGamesForSearchPage($request);
    	$category = $request->input('category');

    	$game_categories = [];
    	$user_owns = null;
    	$user_sells = null;
    	foreach($games as $game) {
    		$list_categories = DB::table('gamecategory')
    							->join('category', 'id', '=', 'idcategory')
    							->where('idgame', $game->id)
    							->select('name')
    							->get();
    		$game_categories[$game->id] = $list_categories;
    		if(Auth::check()) {
    			if(sizeof(DB::table('cart')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) === 0 && 
    				sizeof(DB::table('paid')->join('purchase', 'idpurchase', '=', 'purchase.id')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) === 0) {
    				$user_owns[$game->id] = false;
    			} else {
    				$user_owns[$game->id] = true;
    			}

    			if(sizeof(DB::table('selling')->where('idgame', $game->id)->where('iduser', Auth::user()->id)->get()) === 0) {
    				$user_sells[$game->id] = false;
    			} else {
    				$user_sells[$game->id] = true;
    			}
    		}
		}
		
		$keywords = $request->input('keywords');
		$header = null;
		
		if($category !== null){
			$header = "search?category=".$category;
			if($keywords !== null) 		
				$header = $header."&keywords=".str_replace(' ', '+', $keywords);
		} else if($keywords !== null) 	
			$header = "search?keywords=".str_replace(' ', '+', $keywords);

    	return view('pages.search_page', ['categories' => $categories, 'active_category'=>$category, 'games' => $games, 'game_categories' => $game_categories,
    										'user_owns' => $user_owns, 'user_sells' => $user_sells, 'header' => $header]);
    }
}
