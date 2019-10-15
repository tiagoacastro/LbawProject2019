@extends('layouts.app')

@section('title', 'Apex Games - Homepage')

@section('css_sheet')
  <link rel="stylesheet" href="/css/home_page.css">
@endsection

@section('content')

<div id="carousel" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li data-target="#carousel" data-slide-to="0" class="active"></li>
                <li data-target="#carousel" data-slide-to="1"></li>
                <li data-target="#carousel" data-slide-to="2"></li>
                <li data-target="#carousel" data-slide-to="3"></li>
              </ol>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <a href="{{ route('games', [$rec2games[0]->id]) }}">
                    <img src="{{asset($rec2games[0]->path)}}" alt="{{$rec2games[0]->name}}'s cover" class="img-fluid rounded game_cover d-block w-100">
                  </a>
                </div>
                @foreach($pop2games as $game)
                <div class="carousel-item">
                  <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid rounded game_cover d-block w-100"></a>
                </div>
                @endforeach
                <div class="carousel-item">
                  <a href="{{ route('games', [$rec2games[1]->id]) }}">
                    <img src="{{asset($rec2games[1]->path)}}" alt="{{$rec2games[1]->name}}'s cover" class="img-fluid rounded game_cover d-block w-100">
                  </a>
                </div>
              </div>
              <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>

            <div class="advertisement container mx-auto"></div>

<div class="row categ_row">
          <div class="col-sm-2"></div>
          <div class="col-sm-8">
              <nav id="categories" class="bigger">
                  <ul class="nav justify-content-center">
                    @foreach($categories as $category)
                    <li class="nav-item">
                      <input type="hidden" name="id_category" value="{{ $category->id }}">
                      <a href="search?category={{$category->name}}" class="nav-link">{{ $category->name }}</a>
                    </li> 
                    @endforeach
                  </ul>
              </nav>
          </div>
      </div>

      <h1>Popular</h1>
      <section>
      <div class="row game_row">
        <div class="col-lg-2 col-sm-1"></div>
        @foreach($pop3games as $game)
         <div class="col-12 col-sm">
            <div class="container game" style="cursor:pointer;">
              <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid rounded game"></a>
              <a href="{{ route('games', [$game->id]) }}"><div class="overlay d-none d-md-block rounded"></div></a>
              @if(Auth::check() && $pop3games_owns[$game->id] !== 3)
              <div class="icons d-flex justify-content-between flex-row">
                @if($pop3games_owns[$game->id] !== 1)
                <i class="fas fa-cart-plus addToCartHome d-none d-md-block"></i>
                @endif
                <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                <input type="hidden" name="game" value="{{ $game->id }}">
                @if($pop3games_owns[$game->id] !== 2)
                <i class="fas fa-heart addToFavoritesHome d-none d-md-block"></i>
                @endif
              </div>
              @endif
            </div>
            <div class="text d-flex flex-row justify-content-between name">
                <a href="{{ route('games', [$game->id]) }}"><span>{{ $game->name }}</span></a>
                <span>{{ $game->price }}€</span>
            </div>
        </div>
        @endforeach
        <div class="col-lg-2 col-sm-1"></div>
      </div>
      <div class="row game_row">
          <div class="col-lg-2 col-sm-1"></div>
          @foreach($pop2games as $game)
            <div class="col-12 col-sm">
                  <div class="container game" style="cursor:pointer;">
                      <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid rounded game"></a>
                      <a href="{{ route('games', [$game->id]) }}"><div class="overlay d-none d-md-block rounded"></div></a>
                    @if(Auth::check() && $pop2games_owns[$game->id] !== 3)
                    <div class="icons d-flex justify-content-between flex-row">
                      @if($pop2games_owns[$game->id] !== 1)
                      <i class="fas fa-cart-plus addToCartHome d-none d-md-block"></i>
                      @endif
                      <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                      <input type="hidden" name="game" value="{{ $game->id }}">
                      @if($pop2games_owns[$game->id] !== 2)
                      <i class="fas fa-heart addToFavoritesHome d-none d-md-block"></i>
                      @endif
                    </div>
                    @endif
                  </div>
                  <div class="text d-flex flex-row justify-content-between name">
                      <a href="{{ route('games', [$game->id]) }}"><span>{{ $game->name }}</span></a>
                      <span>{{ $game->price }}€</span>
                  </div>
            </div>
          @endforeach
           
          <div class="col-lg-2 col-sm-1"></div>
        </div>
       </section>
        <h1>Recent Releases</h1>
        <section>
        <div class="row game_row">
            <div class="col-lg-2 col-sm-1"></div>
            @foreach($rec3games as $game)
               <div class="col-12 col-sm">
                <div class="container game" style="cursor:pointer;">
                  <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid rounded game"></a>
                  <a href="{{ route('games', [$game->id]) }}"><div class="overlay d-none d-md-block rounded"></div></a>
                  @if(Auth::check() && $rec3games_owns[$game->id] !== 3)
                    <div class="icons d-flex justify-content-between flex-row">
                      @if($rec3games_owns[$game->id] !== 1)
                      <i class="fas fa-cart-plus addToCartHome d-none d-md-block"></i>
                      @endif
                      <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                      <input type="hidden" name="game" value="{{ $game->id }}">
                      @if($rec3games_owns[$game->id] !== 2)
                      <i class="fas fa-heart addToFavoritesHome d-none d-md-block"></i>
                      @endif
                    </div>
                  @endif
                </div>
                <div class="text d-flex flex-row justify-content-between name">
                    <a href="{{ route('games', [$game->id]) }}"><span>{{ $game->name }}</span></a>
                    <span>{{ $game->price }}€</span>
                </div>
              </div>
            @endforeach
            
            <div class="col-lg-2 col-sm-1"></div>
          </div>
          <div class="row game_row">
            <div class="col-lg-2 col-sm-1"></div>
            @foreach($rec2games as $game)
              <div class="col-12 col-sm">
                <div class="container game" style="cursor:pointer;">
                  <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid rounded game"></a>
                  <a href="{{ route('games', [$game->id]) }}"><div class="overlay d-none d-md-block rounded"></div></a>
                  @if(Auth::check() && $rec2games_owns[$game->id] !== 3)
                    <div class="icons d-flex justify-content-between flex-row">
                      @if($rec2games_owns[$game->id] !== 1)
                      <i class="fas fa-cart-plus addToCartHome d-none d-md-block"></i>
                      @endif
                      <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                      <input type="hidden" name="game" value="{{ $game->id }}">
                      @if($rec2games_owns[$game->id] !== 2)
                      <i class="fas fa-heart addToFavoritesHome d-none d-md-block"></i>
                      @endif
                    </div>
                  @endif
                </div>
                <div class="text d-flex flex-row justify-content-between name">
                  <a href="{{ route('games', [$game->id]) }}"><span>{{ $game->name }}</span></a>
                  <span>{{ $game->price }}€</span>
                </div>
              </div>
            @endforeach
            <div class="col-lg-2 col-sm-1"></div>
          </div>
        </section>
    
@endsection