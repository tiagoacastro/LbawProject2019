@extends('layouts.app')

@section('title', 'Apex Games - Shopping Cart')

@section('css_sheet')
  <link rel="stylesheet" href="/css/cart.css">
@endsection

@section('content')
<div class="row shopping_cart_row">
            <div class="col-sm-2"></div>
            <div class="col-sm px-0 shopping_cart"><h2>My Shopping Cart</h2></div>
        </div>

        <div class="row cart">

        <div class="col-sm-8 major-column">
      @if(sizeof($games) !== 0)
      @foreach($games as $game)
            <div class="row game_row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3 colored px-0" >
                    <a href="{{ route('games', [$game->id]) }}"><img src="{{asset($game->path)}}" alt="{{$game->name}}'s cover" class="img-fluid"></a>
                </div>
                <div class="col-sm-4 colored d-flex align-items-center">
                    <a href="{{ route('games', [$game->id]) }}"> <span class="game_title">{{$game->name}}</span></a>
                </div>
                <div class="col-sm-1 d-flex align-items-center justify-content-center colored">
                    <span><strong>{{$game->price}}&euro;</strong></span>
                </div>
                <div class="col-sm-1 d-flex align-items-center justify-content-center colored" style="padding-top: 6px;">
                    <input type="hidden" name="username" value="{{ Auth::user()->username }}">
                    <input type="hidden" name="game" value="{{ $game->id }}">
                    <button type="button" class="btn btn-info remove removeFromCart">
                        <i class="fas fa-times"><span></span></i>
                    </button>
                </div>
            </div>
        @endforeach
        @else
            <div class="row game_row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 empty-cart" >
                    <h3>Your shopping cart is empty!</h3>
                </div>
            </div>
        @endif
        </div>

        <div class="col-sm-2 checkout-zone major-column">
            <div class="row sized">
                <div class="col-sm-1"> </div>
                <div class="col-sm-10 colored d-flex align-items-center justify-content-center">
                    <h6><strong>Total: {{$sum_prices}}&euro;</strong></h6>
                </div>
                <div class="col-sm-1"> </div>
            </div>
            <div class="row">
                <div class="col-sm-1"> </div>
                <div class="col-sm-10 colored d-flex align-items-center justify-content-center">
                    <form class="form-inline my-2 my-lg-0" action="/users/{{ Auth::user()->username }}/cart" method="post">
                        <div class="input-group align-items-center justify-content-center">
                            {{ csrf_field() }}
                            <label for="nif">Insert NIF:</label><br>
                            <input type="text" name="nif" id="nif" placeholder="NIF" pattern="^(\d{9})$" required autofocus><br>
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-info check_out_button">Checkout</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-1"> </div>
            </div>
            <div class="row">
                <div class="col-sm-1"> </div>
                <div class="col-sm-10 colored d-flex flex-column align-items-center">
                    <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-outline-info"><i class="fas fa-question-circle"></i> Help </button>
                </div>
                <div class="col-sm-1"> </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fas fa-question-circle"></i> Help</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <p class="text-justify">The method available for payment is Paypal.</p>
        <img src="{{asset('/img/logo-paypal.png')}}" class="img-fluid paypal_logo" alt="PayPal">
        <p class="text-justify">To checkout the games in your cart, you'll have to put your NIF in the box that says "NIF" and click on the button named "Checkout". Once the transaction is complete you'll be redirected to your library where you can download the games you checked out.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

 @endsection