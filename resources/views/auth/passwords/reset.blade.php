@extends('layouts.app')

@section('title', 'Apex Games - Password Reset')

@section('css_sheet')
  <link rel="stylesheet" href="/css/password_recovery.css">
@endsection

@section('content')

   
<div class="password_recovery_form">
  <div class="row">
      <div class="col-md-6" id="firstcol">
        <h2> Reset Password </h2>
        <p> Please enter a new password...</p>
      </div>

      <div class="col-md-6" id="secondcol">
        <form method="POST" action="{{ route('password.request') }}">
          {{ csrf_field() }}

       
            <input type="hidden" name="token" value="{{ $token }}">
            <input id="email" type="email" name="email" value="{{ $email or old('email') }}" placeholder="email" required>

            @if ($errors->has('email'))
            <div class="error alert alert-danger"> 
                 <strong>Error: </strong> {{ $errors->first('email') }}
              </div>
            @endif

            <input id="password" type="password" name="password" placeholder="new password" required>
            <input id="password-confirm" type="password" name="password_confirmation" placeholder="password confirmation" required>

            @if ($errors->has('password'))
              <div class="error alert alert-danger"> 
                 <strong>Error: </strong> {{ $errors->first('password') }}
              </div>
            @endif       
      

          <input type="submit" value="Reset Password">

        </form>
        </div>
    </div>
 
</div>
@endsection
