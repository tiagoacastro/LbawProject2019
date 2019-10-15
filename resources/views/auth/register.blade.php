@extends('layouts.app')

@section('title', 'Apex Games - Login')

@section('css_sheet')
  <link rel="stylesheet" href="css/register_page.css">
@endsection

@section('content')
<div class="register_form">

    <div class="container">
      <div class="row">
        <div class="col-md-6" id="firstcol">
          <h2> Join our community </h2>
          <p> Sign up now to be able to buy amazing games and share your creations </p>
        </div>
        <div class="col-md-6" id="secondcol">
          <form method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}
            <input type="text" name="username" placeholder="username" value="{{ old('username') }}" required autofocus>
            @if ($errors->has('username'))
              <span class="error">
                  {{ $errors->first('username') }}
              </span>
            @endif
            <input type="email" id="email" name="email" placeholder="email"  value="{{ old('email') }}" required>
            @if ($errors->has('email'))
              <span class="error">
                  {{ $errors->first('email') }}
              </span>
            @endif
            <input type="password" name="password" placeholder="password" required>
        
            <input type="password" name="password_confirmation" placeholder="repeat password" required>

            @if ($errors->has('password'))
              <div class="error alert alert-danger"> 
                  <strong>Error: </strong> {{ $errors->first('password') }}
              </div>
            @endif
            <input type="submit" value="register">
            <div class="or-seperator"><i>or</i></div>
            <a href="#" class="btn btn-danger btn-block"><i class="fab fa-google"></i> Sign up with <b>Google</b></a>

            <form>
        </div>
      </div>
    </div>
  </div>

  <div class="register_register_link">
    <p>Already have an account? <a href="{{ route('login') }}" class="signin-link">Sign in here.</a></p>
  </div>
@endsection
