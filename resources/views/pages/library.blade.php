@extends('layouts.app')

@section('title', 'Apex Games - Library')

@section('css_sheet')
  <link rel="stylesheet" href="/css/library.css">
@endsection

@section('content')

	<div class="container categories">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm library_title">
                <h2>My Library</h2>
            </div>
        </div>
  	</div>

	@if(sizeof($games) !== 0)
		<div class="container table">
		@foreach($games as $game)
	        <div class="row game_row">
	            <div class="col-sm-2"></div>
	            <div class="col-sm-2 colored px-0" >
	                <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->image_path)}}" alt="{{$game->name}}'s cover" class="img-fluid"></a>
	            </div>
	            <div class="col-sm-4 colored d-flex flex-column align-items-start justify-content-around">
	                <a href="{{ route('games', [$game->id]) }}"><span class="game_title"><strong>{{$game->name}}</strong></span></a>
	                <span class="game_title">Purchase Date: {{date("Y-m-d",strtotime($game->purchasedate))}}</span>
	            </div>
	            <div class="col-sm-2 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
	            	<form action="{{ route('download', ['username' => Auth::user()->username, 'game_id' => $game->id]) }}" method="post">
			          	{!! csrf_field() !!}
			          	<button type="submit" class="btn btn-outline-info"><i class="fas fa-download"></i> <span>Download</span></button>
			          	<input type="hidden" name="_method" value="POST" />
			        </form>
	            </div>
	        </div>
	    @endforeach
	    </div>
    @else
    <div class="row game_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8 colored px-0" >
            <h3>You haven't bought any games yet!</h3>
        </div>
        <div class="col-sm-2"></div>
    </div>
    @endif

 @endsection