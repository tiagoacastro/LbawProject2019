@extends('layouts.app')

@section('title', 'Apex Games - Edit Game Page')

@section('css_sheet')
  <link rel="stylesheet" href="/css/sell_game.css">
@endsection

@section('content')

<form action="/games/{{ $game->id }}/edit" method="post" enctype="multipart/form-data" class="sell_game_form">
    {{ csrf_field() }}
    <div class="form-group">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3 class="display-4">Edit game</h3>
                <label for="game_title">Title</label>
                <p class="display-4"> <input type="text" class="form-control" id="game_title" name="name" placeholder="{{ $game->name }}"> </p>
                @if ($errors->has('name'))
                    <div class="error alert alert-danger"> 
                      <strong>Error: </strong>  {{ $errors->first('name') }}
                    </div>
                @endif
                <label for="brief_d">Brief Description</label>
                <p class="lead"> <input type="text" class="form-control" id="brief_d" name="briefdescription" placeholder="{{ $game->briefdescription }}"></p>
                @if ($errors->has('briefdescription'))
                    <div class="error alert alert-danger"> 
                       <strong>Error: </strong>  {{ $errors->first('briefdescription') }}
                    </div>
                @endif
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image_upload" id="gameImage" aria-describedby="gameImage">
                        <label class="custom-file-label" for="gameImage">Choose image</label>
                    </div>
                </div>
                @if ($errors->has('image_upload'))
                    <div class="error alert alert-danger"> 
                        <strong>Error: </strong>  {{ $errors->first('image_upload') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="overview">
            <div class="container">
                <h2> Overview </h2>
                <div class="row">
                    <div class="col-sm-4">
                        <p></p>
                        <h4> Genres </h4>
                        @foreach($categories as $category)
                            @if($checked_category[$category->id] === 1)
                                <input type="checkbox" name="game-genre[]" id="{{$category->name}}_cat" value="{{$category->name}}" checked><label for="{{$category->name}}_cat">{{$category->name}}</label><br>
                            @else
                                <input type="checkbox" name="game-genre[]" id="{{$category->name}}_cat" value="{{$category->name}}"><label for="{{$category->name}}_cat">{{$category->name}}</label><br>
                            @endif
                        @endforeach
                    </div>
                    <div class="col-sm-4">
                        <p></p>
                        <h4> Release Date </h4> <p>{{ date("Y-m-d", strtotime($game->releasedate)) }}</p>
                        <br><br>
                        <p></p>
                        <h4 class="h4-rating"> Age Restriction </h4>
                        <div class="dropdown">
                            <select class="form-control" name="age-restriction" id="game-rating">
                                @if($game->agerestriction === 3)
                                    <option selected>3+</option>
                                @else
                                    <option>3+</option>
                                @endif
                                @if($game->agerestriction === 7)
                                    <option selected>7+</option>
                                @else
                                    <option>7+</option>
                                @endif
                                @if($game->agerestriction === 12)
                                    <option selected>12+</option>
                                @else
                                    <option>12+</option>
                                @endif
                                @if($game->agerestriction === 16)
                                    <option selected>16+</option>
                                @else
                                    <option>16+</option>
                                @endif
                                @if($game->agerestriction === 18)
                                    <option selected>18+</option>
                                @else
                                    <option>18+</option>
                                @endif
                                @if($game->agerestriction === 0)
                                    <option selected>Everyone</option>
                                @else
                                    <option>Everyone</option>
                                @endif
                            </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                        <p></p>
                        <h4> Developer </h4>
                        @if($developer !== null)
                            <input type="text" id="gameDeveloper" class="form-control" name="gameDeveloper" placeholder="{{ $developer->pen_name }}">
                        @else
                            <input type="text" id="gameDeveloper" class="form-control" name="gameDeveloper" placeholder="{{ $creator->username }}">
                        @endif
                        @if ($errors->has('gameDeveloper'))
                            <div class="error alert alert-danger"> 
                               <strong>Error: </strong>  {{ $errors->first('gameDeveloper') }}
                            </div>
                        @endif
                        <br><br><br>
                        <h4 for="priceGame"> Price </h4>
                        <input type="text" id="priceGame" class="form-control" name="gamePrice" placeholder="{{ $game->price }}">
                        @if ($errors->has('gamePrice'))
                            <div class="error alert alert-danger"> 
                               <strong>Error: </strong>  {{ $errors->first('gamePrice') }}
                            </div>
                        @endif
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="description">
            <div class="container">
                <h2> Description </h2>
                <div class="row">
                    <div class="col-sm-12">
                        <p><textarea class="form-control" name="description" rows="5" id="gameDescription" placeholder="{{ $game->description }}"></textarea></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="submit-btns">
            <button type="submit" class="btn btn-info btn-lg submit">Submit</button>
            <a href="{{ route('games', [$game->id]) }}"><button type="button" class="btn btn-danger cancel">Cancel</button></a><br>
        </div>
    </div>

</form>
@endsection