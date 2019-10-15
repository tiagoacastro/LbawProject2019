@extends('layouts.app')

@section('title', 'Apex Games - Admin Pending Games')

@section('css_sheet')
  <link rel="stylesheet" href="/css/admin_pending_games.css">
@endsection

@section('content')

<div class="col-sm-2 sidebar">
  <ul class="nav flex-column change-flex-mobile">
    <li class="nav-item">
      <a class="nav-link" href="/admin/sales"><i class="fas fa-home"></i> <span class="menu_item">Game sales</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="/admin/pending"><i class="fas fa-gamepad"></i> <span class="menu_item">Pending games</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/users"><i class="fas fa-users"></i> <span class="menu_item">Search user</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/categories"><i class="fas fa-list-ul"></i> <span class="menu_item">Manage categories</span></a>
    </li>
  </ul>
</div>

<h1 id="title">Pending games</h1>


  <div class="container-fluid pending_games_list">

    @foreach ($games as $game)
    <div class="row list_element">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-2 px-0 colored">
                    <a href="/games/{{$game->id}}"><img src="{{$game->path}}" class="img-fluid" alt="image"></a>
                </div>
                <div class="col-sm-3 d-flex justify-content-center align-items-center colored">
                    <a href="/games/{{$game->id}}">{{$game->name}}</a>
                </div>
                <div class="col-sm-2 d-flex justify-content-center align-items-center colored">
                    <a href="/users/{{$game->username}}">By {{$game->username}}</a>
                </div>
                <form action="/admin/games/{{$game->id}}" method="post" class="col-sm-3 d-flex align-items-center justify-content-center colored">
                    {{ csrf_field() }}
                    <select name="value" class="custom-select">
                        <option value="accept" selected>Accept Game</option>
                        <option value="refuse">Refuse Game</option>
                    </select>
                    <button class="btn btn-info">Submit</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach



@endsection