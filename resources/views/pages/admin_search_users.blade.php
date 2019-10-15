@extends('layouts.app')

@section('title', 'Apex Games - Admin Pending Games')

@section('css_sheet')
  <link rel="stylesheet" href="/css/admin_search_users.css">
@endsection

@section('content')
	
	<div class="col-sm-2 sidebar">
	  <ul class="nav flex-column change-flex-mobile">
	    <li class="nav-item">
	      <a class="nav-link" href="/admin/sales"><i class="fas fa-home"></i> <span class="menu_item">Game sales</span></a>
	    </li>
	    <li class="nav-item">
	      <a class="nav-link" href="/admin/pending"><i class="fas fa-gamepad"></i> <span class="menu_item">Pending games</span></a>
	    </li>
	    <li class="nav-item">
	      <a class="nav-link active" href="/admin/users"><i class="fas fa-users"></i> <span class="menu_item">Search user</span></a>
	    </li>
	    <li class="nav-item">
	      <a class="nav-link" href="/admin/categories"><i class="fas fa-list-ul"></i> <span class="menu_item">Manage categories</span></a>
	    </li>
	  </ul>
	</div>
	  
	<h1 id="title">Search User</h1>

	  <div class="container-fluid users_list">

		<div class="row search-bar">
			<div class="col-sm-4"></div>
			<form class="col-sm-6 d-flex flex-row align-items-center" action="/api/admin/users/" method="post">
				{{ csrf_field() }}
				<input class="form-control mr-sm-2" name="searchText" type="search" placeholder="Search" aria-label="Search">
				<button class="btn btn-info"><i class="fas fa-search"></i></button>
			</form>
		</div>

		<div class="results">
	
			@foreach($users as $user)
				<div class="row list_element" data-id="{{ $user->username }}">
					<div class="col-sm-2"></div>
					<div class="col-sm-9">
						<div class="row">
							<div class="col-sm-2"></div>
							<div class="col-sm-1 px-0 colored rounded-left">
								<a href="{{ route('users', $user->username) }}">
									@if($user->path === null)
										<img src="{{asset('/img/avatar.png')}}" class="img-fluid" alt="User">
									@else
										<img src="{{asset($user->path)}}" class="img-fluid" alt="User">
									@endif
								</a>
							</div>
							<div class="col-sm-2 username d-flex justify-content-left align-items-center colored">
								<h4> <a href="{{ route('users', $user->username) }}">{{$user->username}}</a> </h4>
							</div>
							<div class="col-sm-1 name d-flex justify-content-left align-items-center colored">
								<a href="{{ route('users', $user->username) }}">{{$user->name}}</a> 
							</div>
							<div class="col-sm-2 px-0 d-flex button justify-content-end align-items-center colored">
								<a href="{{ route('users', $user->username) }}"><button class="btn btn-outline-info">See Profile</button></a>
							</div>
							@if($user->banned == 'false')
							<div class="col-sm-2 px-0 d-flex button justify-content-start align-items-center colored rounded-right ban">
								<button class="btn btn-danger banUser"><i class="fas fa-ban"></i> Ban User</button>
							</div>
							@else
							<div class="col-sm-2 px-0 d-flex button justify-content-center align-items-center colored rounded-right">
								<h6><strong>Banned</strong></h6>
							</div>
							@endif
						</div>
					</div>
				</div>
			@endforeach

		</div>

	</div>

@endsection
