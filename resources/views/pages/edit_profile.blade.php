@extends('layouts.app')

@section('title', 'Apex Games - Edit Profile')

@section('css_sheet')
  <link rel="stylesheet" href="/css/edit_profile.css">
@endsection

@section('content')
<div id="container">
    <img class="profileBackground" src="\img/background.jpg" alt="Background Profile Image">
    <span class="profileName"> <img class="profileImage" src="{{ $image->path }}"> &ensp;{{ $user->username }} </span>
  </div>


  <form action="/users/{{ $user->username }}" method="post" enctype="multipart/form-data" class="editProfileForm">
    {{ csrf_field() }}
    <div class="container" id="profileInfo">
      <h2> User Info </h2>
      <div class="row">
        <div class="col-md-6" id="firstcol">
        <br><br><p> <b>Name</b> </p>
          <input type="text" name="name" placeholder="{{ $normal_user->name }}">
            @if ($errors->has('name'))
              <div class="error alert alert-danger"> 
                 <strong>Error: </strong>  {{ $errors->first('name') }}
              </div>
            @endif
          <br><br><p> <b>Email</b> </p>
          <input type="text" name="email" placeholder="{{ $user->email }}">
          @if ($errors->has('email'))
              <div class="error alert alert-danger"> 
                <strong>Error: </strong> {{ $errors->first('email') }}
              </div>
            @endif
        </div>
        <div class="col-md-6" id="secondcol">
        <br><br><p> <b>New Password</b></p>
          <input type="password" name="password">
        
          <br><br><p> <b>Repeat Password</b> </p>
          <input type="password" name="password_confirmation">
          @if ($errors->has('password'))
              <div class="error alert alert-danger"> 
                 <strong>Error: </strong> {{ $errors->first('password') }}
              </div>
            @endif
        </div>
      </div>
      <div class="row" id="buttons">
        <div class="col-md-12 infoBtns">
          <form action="" method="post">
          <p>
          <span onclick="callFileBtn()" class="btn btn-info changeImage"> Choose Profile Picture </span>
          <script type="text/javascript">
            function callFileBtn(){
                document.getElementById("file_upload").click();
            }</script>
          <input type="file" id="file_upload" name="file_upload"><br>
          <input type="submit" value="Upload Picture"></p>
          </form><br>
          <div class="updateBtns">
          <input type="submit" value="Update"  class="submitEdit">
          <a href="{!! route('users', ['username' => $user->username]) !!}"><button class="btn btn-sm btn-danger" type="button" name="btnCancel"> Cancel </button></a>
          </div>
        </div>
      </div>

    </div>
  </form>
@endsection