@extends('layouts.app')

@section('title', 'Apex Games - Login')

@section('css_sheet')
  <link rel="stylesheet" href="css/login_page.css">
@endsection

@section('content')

<div class="login_form">
      <div class="row">
        <div class="col-md-6" id="firstcol">
          <h2> Welcome back </h2>
          <p> Login now to be able to buy amazing games and share your creations </p>
        </div>
        <div class="col-md-6" id="secondcol">
          <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <input type="text" name="username" placeholder="username" required autofocus>
       
            <input type="password" name="password" placeholder="password" required>
            @if ($errors->has('password')|| $errors->has('username')) 
              <div class="error alert alert-danger"> 
                  <strong>Error: </strong> {{ $errors->first('username') }}
              </div>
            @endif
            <input type="submit" value="Login">

            @if ($errors->has('banned'))
            <div class="error alert alert-danger"> 
                  <strong>Error: </strong> {{ $errors->first('banned') }}
              </div>
            @endif

            <div class="forgot_password">
              <br><a href="{{ route('password.request') }}"> Forgot password ?</a>
            </div>



            <div class="or-seperator"><i>or</i></div>
            <a href="#" class="btn btn-danger btn-block"><i class="fab fa-google"></i> Sign in with <b>Google</b></a>
          </form>
        </div>

      </div>
    </div>



    <div class="login_register_link">
      <p>Don't have an account? <a href="register" class="signup-link">Sign up here.</a></p>
    </div>
@endsection
