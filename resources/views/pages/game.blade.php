@extends('layouts.app')

@section('title', 'Apex Games - Game Page')

@section('css_sheet')
    <link rel="stylesheet" href="/css/game_page.css">
    <style>
      .jumbotron.jumbotron-fluid.game {
        background-image: url("{{$game->imagepath}}");
      }
    </style>
@endsection

@section('content')
{{ csrf_field() }}

<div class="jumbotron jumbotron-fluid game">
    <div class="container shade_background">
      <h1 class="display-4">{{$game->name}}</h1>
      <p class="lead text-justify">{{$game->briefdescription}}</p>
      <div class="score">
        @for($i = 1; $i <= $game->score; $i++)
            <span class="fa fa-star checked"></span>
        @endfor
        @for($i = $game->score; $i < 5; $i++)
            <span class="fa fa-star"></span>
        @endfor
      </div>
    </div>
  </div>


  <div class="overview">
    <div class="container">
      <h2> Overview </h2>
      <div class="row">
        <div class="col-sm-4">
            @if(sizeof($genres) !== 0)
                <p>
                    <h4> Genre </h4>

                    <nav id="categories" class="smaller">
                        <ul class="nav">
                            @foreach($genres as $genre)
                                <li class="nav-item"><a href="/search?category={{$genre->name}}" class="nav-link">{{$genre->name}}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                </p>
            @endif
          <p>
            <h4> Developer </h4> 
            @if($game->pen_name != null)
                {{$game->pen_name}}
            @else
                {{$creator->username}}
            @endif
          </p>
        </div>
        <div class="col-sm-4">
          <p>
            <h4> Release Date </h4> {{date("Y-m-d", strtotime($game->releasedate))}}
          </p>
          <br>
          <p>
            <h4> Age Restriction </h4> {{$game->agerestriction}}+
          </p>
        </div>
        <div class="col-sm-4" id="price">
          <p>
            <h4> {{$game->price}}€ </h4>
          </p>

          <!-- MISSING -->
          @if(Auth::check() && !Auth::guard('admin')->check())
            <input type="hidden" name="username" value="{{ Auth::user()->username }}">
          <input type="hidden" name="game" value="{{ $game->id }}">
          <input type="hidden" name="user_image" value="{{ $auth_user->path }}">
          @endif
          @if(Auth::check() && !Auth::guard('admin')->check() && Auth::user()->id !== $creator->id)
            @if($has_favorite === 1)
              <button type="button" class="btn btn-info favourites removeFromFavoritesGame"> <i class="fas fa-heart-broken"></i></button>
            @else
              <button type="button" class="btn btn-info favourites addToFavoritesGame"> <i class="fas fa-heart"></i></button>
            @endif
            @if($has_game === 0)
              <button type="button" class="btn btn-info addToCartGame">Add to Cart</button> <br>
            @endif
          @endif
          @if((Auth::check() && !Auth::guard('admin')->check() && Auth::user()->id === $creator->id) || Auth::guard('admin')->check())
          <a href="{{ route('games.edit', [$game->id]) }}"><button type="button" class="btn btn-outline-info">Edit Game</button> </a>
           <form action="{{ route('games', ['id' => $game->id]) }}" method="post">
              {!! csrf_field() !!}
              <button class="btn btn-outline-danger" name="btnDeleteGame"><i class="fas fa-trash-alt"></i> Delete Game </button>
              <input type="hidden" name="_method" value="DELETE" />
            </form>
          @endif

          <!-- TILL -->

        </div>
      </div>

    </div>
  </div>

  <div class="description">
    <div class="container">
      <h2> Description </h2>
      <div class="row">
        <div class="col-sm-12">
          <p class="text-justify">
            {{$game->description}}
          </p>
        </div>
      </div>

    </div>
  </div>

  <div class="reviews">
    <hr>
    <div class="container">
      <h2> User Reviews </h2>

      <section id="userReviews">
        
      @if(sizeof($reviews) !== 0)
        @foreach($reviews as $review)
            <div class="review1 user_reviews">

                <div class="row">
                <div class="col-sm-8">
                    @if($review->path != null)
                      <p class="user"> <img class="avatar1" alt="{{$review->username}}'s image" src="{{$review->path}}">&ensp; {{$review->username}} </p>
                    @else
                      <p class="user"> <img class="avatar1" alt="{{$review->username}}'s image" src="/img/avatar.png">&ensp; {{$review->username}} </p>
                    @endif
                </div>
                <div class="col-sm-4">
                    <p>
                    <div class="review-score">
                      <input type="hidden" name="review_score" value="{{ $review->score }}">
                    @for($i = 1; $i <= $review->score; $i++)
                        <span class="fa fa-star checked"></span>
                    @endfor
                    @for($i = $review->score; $i < 5; $i++)
                        <span class="fa fa-star"></span>
                    @endfor
                    </div>
                    </p>
                </div>
                </div>
                
                <p class="review-text text-justify">{{$review->content}}</p>

                <div class="row">
                <div class="col-sm-8 vote">

                    <input type="hidden" name="game" value="{{$game->id}}">
                    <input type="hidden" name="user" value="{{$review->iduser}}">

                    @if(Auth::check() && !Auth::guard('admin')->check())

                      @if($review->type === null || $review->type == false)
                        <button type="button" class="btn btn-info upvote"><i class="far fa-thumbs-up"></i></button>
                      @else
                        <button type="button" class="btn btn-info upvote"><i class="fas fa-thumbs-up"></i></button>
                      @endif

                      @if($review->type === null || $review->type == true)
                        <button type="button" class="btn btn-info downvote"><i class="far fa-thumbs-down"></i></button>
                      @else
                        <button type="button" class="btn btn-info downvote"><i class="fas fa-thumbs-down"></i></button>
                      @endif

                    @endif

                    <span> Points:</span><span class="voteNumber">{{$review->votes}}</span>

                </div>
                <div class="col-sm-4">
                    <p class="date"><span class="reviewDate"><i class="far fa-clock"></i> {{date("Y-m-d",strtotime($review->creationdate))}} </span></p>
                </div>
                </div>
            </div>

            <br>
        @endforeach
      
      </section>

      @endif

      <!-- MISSING -->

      <p class="moreContent">
        <input type="hidden" name="game" value="{{$game->id}}">
        @if(sizeof($reviews) !== 0)
        <button type="button" class="btn btn-info loadReviews"> Load more </button>
        @endif
        @if(Auth::check() && !Auth::guard('admin')->check() && $has_review === 0)
          <button type="button" class="btn btn-info addReview"> Add a review </button>
        @elseif(Auth::check() && !Auth::guard('admin')->check())
          <button type="button" onclick="editReviewForm()" class="btn btn-info editReview"> Edit your review </button>
          <form action="{{ route('reviews.delete', ['id' => $game->id]) }}" method="post">
            {!! csrf_field() !!}
            <button type="submit" class="btn btn-danger removeReview"> Remove your review </button>
            <input type="hidden" name="_method" value="DELETE" />
        </form>
        @endif
      </p>
    </div>
  </div>
  @endsection