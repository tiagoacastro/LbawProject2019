@extends('layouts.app')

@section('title', 'Apex Games - Password Recovery')

@section('css_sheet')
  <link rel="stylesheet" href="../css/password_recovery.css">
@endsection

@section('content')



<div class="password_recovery_form">
<div class="row">
    <div class="col-md-6" id="firstcol">
        <h2> Password Recovery </h2>
        <p> Forgot your password? Don't worry... Please enter your email to recover it</p>
    </div>
    <div class="col-md-6" id="secondcol">

        <form method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}


        <label for="email" class="control-label">E-Mail Address</label>
        <input id = "email" type="email" name="email" placeholder="email" required>
        @if ($errors->has('email'))
            <div class="error alert alert-danger"> 
                 <strong>Error: </strong> {{ $errors->first('email') }}
              </div>
            @endif
   
        <input type="submit" value="Send Password Reset Link">
      
    
        </form>
    </div>
         

</div>




@endsection




