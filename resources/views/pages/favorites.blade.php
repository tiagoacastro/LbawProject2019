@extends('layouts.app')

@section('title', 'Apex Games - Favorites')

@section('css_sheet')
  <link rel="stylesheet" href="/css/favorites.css">
@endsection

@section('content')
<div class="row favorites_row">
        <div class="col-sm-2"></div>
        <div class="col-sm px-0 favorites_title">
            <h2>My Favorite Games</h2>
        </div>
    </div>

    @if(sizeof($wish_list) + sizeof($wish_list_cart) !== 0)
      @foreach($wish_list as $wish_list_item)

      <div class="row game_row">
          <div class="col-sm-2"></div>
          <div class="col-sm-2 colored px-0" >
               <a href="{{ route('games', [$wish_list_item->id]) }}"><img src="{{asset($wish_list_item->path)}}" alt="{{$wish_list_item->name}}'s cover" class="img-fluid"></a>
          </div>
          <div class="col-sm-3 g_title colored d-flex align-items-center">
              <a href="{{ route('games', [$wish_list_item->id]) }}"><span class="game_title">{{$wish_list_item->name}}</span></a>
          </div>
          <div class="col-sm-1 price colored d-flex align-items-center">
              <span><strong>{{$wish_list_item->price}}&euro;</strong></span>
          </div>
          <div class="col-sm-2 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
              <button type="button" class="btn btn-info addToCart">Add to Cart</button>
              <input type="hidden" name="username" value="{{ Auth::user()->username }}">
              <input type="hidden" name="game" value="{{ $wish_list_item->id }}">
              <button type="button" class="btn btn-info removeFromFavorites">
                  <i class="fas fa-heart-broken"></i>
              </button>
          </div>
      </div>

      @endforeach
      @foreach($wish_list_cart as $wish_list_item_cart)

      <div class="row game_row">
          <div class="col-sm-2"></div>
          <div class="col-sm-2 colored px-0" >
              <a href="{{ route('games', [$wish_list_item_cart->id]) }}">
                <img src="{{asset($wish_list_item_cart->path)}}" alt="{{$wish_list_item_cart->name}}'s cover" class="img-fluid">
              </a>
          </div>
          <div class="col-sm-3 g_title colored d-flex align-items-center">
            <a href="{{ route('games', [$wish_list_item_cart->id]) }}"><span class="game_title">{{$wish_list_item_cart->name}}</span></a>
          </div>
          <div class="col-sm-1 price colored d-flex align-items-center">
              <span><strong>{{$wish_list_item_cart->price}}&euro;</strong></span>
          </div>
          <div class="col-sm-2 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
              <input type="hidden" name="username" value="{{ Auth::user()->username }}">
              <input type="hidden" name="game" value="{{ $wish_list_item_cart->id }}">
              <button type="button" class="btn btn-info removeFromFavorites">
                  <i class="fas fa-heart-broken"></i>
              </button>
          </div>
      </div>

      @endforeach
    @else
    <div class="row game_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8 colored px-0" >
            <h3>You haven't selected your favorite games!</h3>
        </div>
        <div class="col-sm-2"></div>
    </div>
    @endif

    <div class="row owned bought">
        <div class="col-sm-2"></div>
        <div class="col-sm px-0">
            <h2>My Favorite Bought Games </h2>
        </div>
    </div>

     @if(sizeof($favorites) !== 0)
      @foreach($favorites as $favorite)

      <div class="row owned game_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-2 colored px-0" >
            <a href="{{ route('games', [$favorite->id]) }}"><img src="{{asset($favorite->path)}}" alt="{{$favorite->name}}'s cover" class="img-fluid"></a>
        </div>
        <div class="col-sm-3 colored g_title d-flex align-items-center">
            <a href="{{ route('games', [$favorite->id]) }}"><span class="game_title">{{$favorite->name}}</span></a>
        </div>
        <div class="col-sm-1 colored"></div>
        <div class="col-sm-2 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
            <a href="{{ route('users.library', [Auth::user()->username]) }}" ><button type="button" class="btn btn-info">View Library</button></a>
            <input type="hidden" name="username" value="{{ Auth::user()->username }}">
            <input type="hidden" name="game" value="{{ $favorite->id }}">
            <button type="button" class="btn btn-info removeFromFavorites">
                <i class="fas fa-heart-broken"></i>
            </button>
        </div>
    </div>

      @endforeach
    @else
    <div class="row game_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8 colored px-0" >
            <h3>You haven't selected your favorite owned games!</h3>
        </div>
        <div class="col-sm-2"></div>
    </div>
    @endif

 @endsection