@extends('layouts.app')

@section('title', 'Apex Games - Search Page')

@section('css_sheet')
  <link rel="stylesheet" href="/css/search_page.css">
@endsection

@section('content')
<div class="row categ_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <nav id="categories" class="bigger">
                <ul class="nav justify-content-center">
                    @foreach($categories as $category)
                      @if($active_category == $category->name)
                      <li class="nav-item active">
                      @else
                      <li class="nav-item">
                      @endif
                        <input type="hidden" name="id_category" value="{{ $category->id }}">
                        @if($header !== null)
                            <a href="{{$header}}&category={{$category->name}}" class="nav-link">{{ $category->name }}</a>
                        @else
                            <a href="search?category={{$category->name}}" class="nav-link">{{ $category->name }}</a>
                        @endif
                      </li> 
                    @endforeach
                    @if($header !== null)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Sort by</a>
                            <div class="dropdown-menu sort">
                                <a class="dropdown-item" href="{{$header}}&order=price">Price</a>
                                <a class="dropdown-item" href="{{$header}}&order=score">Score</a>
                                <a class="dropdown-item" href="{{$header}}&order=releasedate">Release Date</a>
                            </div>
                        </li>
                    @else 
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Sort by</a>
                            <div class="dropdown-menu sort">
                                <a class="dropdown-item" href="search?order=price">Price</a>
                                <a class="dropdown-item" href="search?&order=score">Score</a>
                                <a class="dropdown-item" href="search?&order=releasedate">Release Date</a>
                            </div>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>

    <div class="container gamesTable">
    @if(sizeof($games) === 0)
        <div class="row game_row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 d-flex justify-content-center" >
                <h3>There are no results!</h3>
            </div>
        </div>
    @else
    @foreach($games as $game)
    <div class="row outside_row">
        <div class="col-sm-1"></div>
        <div class="col-sm-3 game rounded-left px-0">
            <a href="{{ route('games', [$game->id]) }}"><img src="{{ asset($game->path) }}" alt="{{$game->name}}'s cover" class="img-fluid"></a>
        </div>
        <div class="col-sm-4 game">
            <div class="row row_title">
                <div class="col-sm-12">
                    <a href="{{ route('games', [$game->id]) }}"><h4 class="title">{{$game->name}}</h4></a>
                </div>
            </div>
            <div class="row row_date">
                <div class="col-sm-12">
                    <span><small class="text-muted">Release Date: {{date("Y-m-d",strtotime($game->releasedate))}}</small></span>
                </div>
            </div>
            <div class="row row_middle">
                <div class="col-sm-12 d-flex align-items-center">
                    <nav class="categories smaller">
                        <ul class="nav">
                          @foreach($game_categories[$game->id] as $game_category)
                            <li class="nav-item"><a href="search?category={{$game_category->name}}" class="nav-link">#{{$game_category->name}}</a></li>
                          @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-3 game rounded-right">
            <div class="row h-100">
                <div class="col-sm-4"></div>
                <div class="col-sm-8 h-100 text-center d-flex flex-column align-items-center justify-content-center">
                  @if(Auth::check() && $user_owns[$game->id] === false && $user_sells[$game->id] === false)
                    <button type="button" class="btn btn-info addToCart">Add to Cart</button>
                    <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                    <input type="hidden" name="game" value="{{ $game->id }}">
                  @elseif(Auth::check() && $user_sells[$game->id] === true)
                    <a href="{{ route('games.edit', [$game->id]) }}" >
                        <button type="button" class="btn btn-outline-info"><span>Edit Game</span></button>
                    </a>
                  @elseif(Auth::check() && $user_owns[$game->id] === true)
                    <a href="{{ route('users.library', [Auth::user()->username]) }}" ><button type="button" class="btn btn-outline-info">View Library</button></a>
                  @endif
                    <p><strong>{{$game->price}} &euro;</strong></p>

                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    </div>

 @endsection