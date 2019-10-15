@extends('layouts.app')

@section('title', 'Apex Games - Sell Game Page')

@section('css_sheet')
  <link rel="stylesheet" href="/css/sell_game.css">
@endsection

@section('content')

<form action="/games/sell" method="post" enctype="multipart/form-data" class="sell_game_form">
    {{ csrf_field() }}
    <div class="form-group">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3 class="display-4"> Sell your game</h3>
                <label for="game_title">Title *</label>
                <p class="display-4"> 
                    <input type="text" class="form-control" id="game_title" name="name" placeholder="Insert Game Title" required> 
                </p>
                @if ($errors->has('name'))
                    <div class="error alert alert-danger"> 
                      <strong>Error: </strong>  {{ $errors->first('name') }}
                    </div>
                @endif
                <label for="brief_d">Brief Description *</label>
                <p><input type="text" class="form-control" id="brief_d" name="briefdescription" placeholder="Insert a brief description"></p>
                @if ($errors->has('briefdescription'))
                    <div class="error alert alert-danger"> 
                       <strong>Error: </strong>  {{ $errors->first('briefdescription') }}
                    </div>
                @endif
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image_upload" id="gameImage" aria-describedby="gameImage">
                        <label class="custom-file-label" for="gameImage">Choose image *</label>
                    </div>
                </div>
                @if ($errors->has('image_upload'))
                    <div class="error alert alert-danger"> 
                        <strong>Error: </strong>  {{ $errors->first('image_upload') }}
                    </div>
                @endif
                <p></p>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="gameFile" name="game_upload" aria-describedby="gameImage">
                        <label class="custom-file-label" for="gameFile">Choose executable file *</label>
                    </div>
                </div>
                @if ($errors->has('game_upload'))
                    <div class="error alert alert-danger"> 
                        <strong>Error: </strong>  {{ $errors->first('game_upload') }}
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
                        <input type="checkbox" name="game-genre[]" id="{{$category->name}}_cat" value="{{$category->name}}"><label for="{{$category->name}}_cat">{{$category->name}}</label><br>
                        @endforeach
                    </div>
                    <div class="col-sm-4">
                        <p></p>
                        <h4> Release Date </h4> <p>{{ date("Y-m-d") }}</p>
                        <br><br>
                        <p></p>
                        <h4 class="h4-rating"> Age Restriction * </h4>
                        <div class="dropdown">
                            <select class="form-control" name="age-restriction" size="1" id="game-rating">
                                <option>3+</option>
                                <option>7+</option>
                                <option>12+</option>
                                <option>16+</option>
                                <option>18+</option>
                                <option>Everyone</option>
                            </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                        <p></p>
                        <h4> Developer </h4>
                        <input type="text" id="gameDeveloper" class="form-control" name="gameDeveloper" placeholder="Insert name to be exposed as developer.">
                        @if ($errors->has('gameDeveloper'))
                            <div class="error alert alert-danger"> 
                               <strong>Error: </strong>  {{ $errors->first('gameDeveloper') }}
                            </div>
                        @endif
                        <br><br><br>
                        <h4> Price * </h4>
                        <input type="text" id="priceGame" class="form-control" name="gamePrice" placeholder="Insert Price in €." required>
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
                <h2> Description *</h2>
                <div class="row">
                    <div class="col-sm-12">
                        <p><textarea class="form-control" name="description" rows="5" id="gameDescription"></textarea></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="submit-btns">
            <button type="submit" class="btn btn-info btn-lg submit">Submit</button>
            <a href="{{route('homepage')}}"><button type="button" class="btn btn-danger cancel">Cancel</button></a>
        </div>
    </div>

</form>    
@endsection