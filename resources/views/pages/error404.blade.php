@extends('layouts.app')

@section('title', 'Apex Games - Error 404')

@section('css_sheet')
  <link rel="stylesheet" href="/css/error_page.css">
@endsection

@section('content')
	<div class="row error_row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8 d-flex flex-column colored">
            <h3 class="oops">Oops! The page you're looking for doesn't exist.</h3>
            <span><small>Error 404</small></span>
            <a href="{{ route('homepage') }}"><button type="button" class="btn btn-info">Go Back to Homepage</button></a>
        </div>
    </div>
@endsection