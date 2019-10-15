<!DOCTYPE html>
<html lang="en">

<head>
  <title>@yield('title', 'Apex Games')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/ico" href="{{ asset('img/minilogo.ico') }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="{{ asset('js/app.js') }}" defer></script>
  @yield('css_sheet')


  <link rel="stylesheet" href="/css/print.css">
  <link rel="stylesheet" href="/css/style.css">

  @if (Auth::guard('admin')->check() && substr(Route::current()->getName(), 0, 5) === 'admin')
      <link rel="stylesheet" href="/css/admin_navbar.css">
  @endif

</head>

<body>
<div id="page_container">
  <div id="content_wrap">
  <header>
    <nav class="navbar navbar-expand-md fixed-top navbar-light navbar-inverse no-print">
      <div class="container-fluid">
        <!-- LOGO -->
        <div class="navbar-header">
          <a class="navbar-brand mr-auto" href="{{ route('homepage') }}">
            <img src="{{ asset('/img/logo3.png') }}" width="120" height="60" alt="logo">
          </a>
        </div>

        
        <!-- HAMBURGER MENU -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNavbar" aria-controls="myNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="myNavbar">

            <!-- SEARCH-->
            <form class="form-inline my-2 my-lg-0" action="/search" method="get">
              <div class="input-group">
                <input class="form-control mr-sm-2" type="search" name="keywords" placeholder="Search">
                <div class="input-group-btn">
                  <button class="btn btn-default" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          <ul class="navbar-nav justify-content-right">
          @if(Auth::guard('admin')->check())
            <!-- DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="btn btn-default dropdown-toggle" href="#" role="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <p class="dropdown-item">Signed in as <b>Administrator_John</b></p>
                <hr>
                  <a class="dropdown-item" href="{{ route('admin.sales') }}">Admin Dashboard</a>
                  <a class="dropdown-item" href="{{ route('logout') }}">Sign out</a>
              </div>
            </li>
            @elseif(Auth::check())

            <li class="nav-item active">
              <a href="{{ route('users.favorites', [Auth::user()->username]) }}"><i class="fas fa-heart"> <span> Favorites </span></i></a>
            </li>

            <li class="nav-item active">
              <a href="{{ route('users.cart', [Auth::user()->username]) }}"><i class="fas fa-shopping-cart"> <span> Cart </span></i></a>
            </li>

            <!-- DROPDOWN -->
            <li class="nav-item dropdown">
              <!-- <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> -->
              <a class="btn btn-default dropdown-toggle" href="#" role="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle"></i>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <p class="dropdown-item">Signed in as <b>{{ Auth::user()->username }}</b></p>
                <hr>
                <a class="dropdown-item" href="{{ route('users', [Auth::user()->username]) }}">My Profile</a>
                <a class="dropdown-item" href="{{ route('users.library', [Auth::user()->username]) }}">My Library</a>
                <a class="dropdown-item" href="{{ route('users.purchases', [Auth::user()->username]) }}">Purchase History</a>
                <a class="dropdown-item" href="{{ route('games.sell') }}">Sell Game</a>
                <a class="dropdown-item" href="{{ route('users.sell', [Auth::user()->username]) }}">My Games For Sale</a>
                <hr>
                <a class="dropdown-item" href="{{ route('logout') }}">Sign out</a>
              </div>
            </li>

            @else
            <!-- DROPDOWN -->
            <li class="nav-item dropdown">
              <!-- <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> -->
              <a class="btn btn-default dropdown-toggle" href="#" role="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle"></i>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <p class="dropdown-item"> 
                  <a href="{{ route('login') }}" class="login-link"><u>Login</u> </a> /
                  <a href="{{ route('register') }}" class="register-link">Sign up </a></p>
              </div>
            </li>
            @endif

          </ul>
        </div>
      </div>
      </nav>
  </header>

  @yield('content')
    
    </div>
    <footer>

      <div class="container">

        <div class="row">
          <div class="col-md-6 no-print">
            <h6 class="footer-title"> Help </h6>
            <ul class="list-unstyled">
              <li>
                <a href="{{ route('about') }}">About</a>
              </li>
              <li>
                <a href="{{ route('faq') }}">FAQ</a>
              </li>
            </ul>
          </div>

          <div class="col-md-6 ">
            <h6 class="contacts-title"> Contacts </h6>
            <ul class="list-unstyled contacts">
              <li>
                <i class="fas fa-envelope"></i> apex_games@gmail.com
              </li>
              <li>
                <i class="fas fa-mobile-alt"></i> +351992511125
              </li>
            </ul>

            <p class="copyright">© Copyright 2019 Apex Games. All rights reserved.</p>
          </div>

        </div>

      </div>

    </footer>
    </div>
</body> 
</html>
