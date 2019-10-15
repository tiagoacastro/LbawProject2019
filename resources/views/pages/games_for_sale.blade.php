@extends('layouts.app')

@section('title', 'Apex Games - My Games For Sale')

@section('css_sheet')
  <link rel="stylesheet" href="/css/games_for_sale.css">
@endsection

@section('content')
<div class="container table">
    <div class="row page_title_row">
        <div class="col-sm-1"></div>
        <div class="col-sm px-0 page_title">
            <h2 class="games_title">My Games For Sale</h2>
        </div>
    </div>
    @if(sizeof($games) !== 0)
        @foreach($games as $game)
            <div class="row game_row">
                <div class="col-sm-1"></div>
                <div class="col-sm-2 colored px-0" >
                    @if($game->state =='Accepted' || $game->state == 'Pending')
                        <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid"></a>
                    @else
                        <img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid">
                    @endif
                </div>
                <div class="col-sm-4 colored d-flex flex-column align-items-start justify-content-around">
                    @if($game->state =='Accepted' || $game->state == 'Pending')
                        <a href="{{ route('games', [$game->id]) }}"><span class="game_title"><strong>{{$game->name}}</strong></span></a>
                    @else
                        <span class="game_title"><strong>{{$game->name}}</strong></span>
                    @endif
                    <span class="game_title">Uploading Date: {{date("Y-m-d",strtotime($game->releasedate))}}</span>
                </div>
                <div class="col-sm-2 colored d-flex justify-content-center align-items-center">
                    @if($game->state == 'Accepted')
                        <span class="state_a">{{$game->state}}</span>
                    @elseif($game->state == 'Pending')
                        <span class="state_p">{{$game->state}}</span>
                    @else
                        <span class="state_r">{{$game->state}}</span>
                    @endif
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
                    @if($game->state == 'Accepted')
                        <a href="{{ route('games.edit', [$game->id]) }}"><button type="button" class="btn btn-outline-info"><span>Edit Game</span></button></a>
                    @elseif($game->state == 'Pending' || $game->state == 'Rejected')
                        <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                        <input type="hidden" name="game" value="{{ $game->id }}">
                        <button type="button" class="btn btn-outline-info deleteGameRequest"><span>Delete Request</span></button>
                    @endif
                </div>
            </div>
        @endforeach
    @else
    <div class="row game_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8 px-0" >
            <h3>You don't have games on sale!</h3>
        </div>
        <div class="col-sm-2"></div>
    </div>
    @endif
 @endsection