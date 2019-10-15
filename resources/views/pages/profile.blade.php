@extends('layouts.app')

@section('title', 'Apex Games - Profile')

@section('css_sheet')
  <link rel="stylesheet" href="../css/profile_page.css">
@endsection

@section('content')
<div id="container">
    <img class="profileBackground" src="/img/background.jpg" alt="Background Profile Image">
    <span class="profileName"> <img class="profileImage" alt="background image" src="{{ $image->path }}"> &ensp;{{ $user->username }} @IF($normal_user->name !== null) ({{ $normal_user->name }}) @ENDIF </span>
  </div>


  <div class="container" id="profileInfo">
  <h2> User Info </h2>
    <div class="edit_form">
    <div class="row">
      <div class="col-md-6">
        <p> <b>Email</b> <br> {{ $user->email }} </p>
        <p> <b>Member since: </b> <br> {{ date("Y-m-d",strtotime($normal_user->joindate)) }} </p>
        <p> <b>Number of bought games: </b> <br> {{ sizeof($purchases) }} </p>
      </div>
      <div class="col-md-6 infoBtns">
        @if(!Auth::guard('admin')->check())
        <a href="{{ route('users.edit', ['username' => $user->username]) }}"><button class="btn btn-info editProfile" name="btnEditProfile"> <i class="fas fa-user-edit"></i> Edit Profile </button></a>
        @endif
        <br>
        @if(!Auth::guard('admin')->check())
        <form action="{{ route('users', ['username' => $user->username]) }}" method="post">
          {!! csrf_field() !!}
          <button class="btn btn-outline-danger" name="btnDeleteAccount"><i class="fas fa-trash-alt"></i> Delete Account </button>
          <input type="hidden" name="_method" value="DELETE" />
        </form>
        @endif
        <br>
      </div>
    </div>
  </div>

  </div>

  @if(sizeof($purchases) !== 0)

  <div class="container" id="userTables">
  <h2> Recently bought games </h2>

  <div class="row">
      <div class="col-sm-12">
          <table class="table">
              <thead>
                  <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Product</th>
                  <th scope="col">Paid</th>
                  </tr>
              </thead>
              <tbody>
              	@foreach($purchases as $purchase)
              	<tr>
                  <td>{{$purchase->purchasedate}}</td>
                  <td>{{$purchase->name}}</td>
                  <td>{{$purchase->value}}</td>
                </tr>
              	@endforeach
              </tbody>
          </table>
      </div>
      <div class="col-sm-2"></div>
  </div>
  </div>

@if(!Auth::guard('admin')->check())
  <div class="btnPurschaseHistory">
  <a href="{{ route('users.purchases', [$user->username]) }}"><button class="btn btn-info" name="btnPurschaseHistory"> See More </button></a>
  </div>
@endif
@endif
 @endsection