@extends('layouts.app')

@section('title', 'Apex Games - Admin Manage Categories')

@section('css_sheet')
  <link rel="stylesheet" href="/css/admin_categories.css">
@endsection

@section('content')

<div class="col-sm-2 sidebar">
  <ul class="nav flex-column change-flex-mobile">
    <li class="nav-item">
      <a class="nav-link" href="/admin/sales"><i class="fas fa-home"></i> <span class="menu_item">Game sales</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/pending"><i class="fas fa-gamepad"></i> <span class="menu_item">Pending games</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/users"><i class="fas fa-users"></i> <span class="menu_item">Search user</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="/admin/categories"><i class="fas fa-list-ul"></i> <span class="menu_item">Manage categories</span></a>
    </li>
  </ul>
</div>

<div id="header">
    <h1 id="title">Manage categories</h1>
</div>


<div class="container-fluid categories_list">
    <div class="row list_element">

        <div class="col-sm-3"></div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-1"></div>
                <form id="addCategory" action="/api/admin/categories/add" method="post" class="col-sm-6 d-flex flex-row align-items-center">
                    {{ csrf_field() }}
                    <input class="form-control col-sm-9" name="categoryName" type="text" placeholder="name" aria-label="text">
                    <button class="btn btn-info">Add category</button>
                </form>
            </div>
    </div>
    </div>

    @foreach ($categories as $categorie)
    <div class="row list_element">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-3 d-flex align-items-center colored">
                   {{$categorie->name}}
                </div>
                <div class="col-sm-1 d-flex colored"></div>
                <div class="col-sm-1 d-flex justify-content-center align-items-center colored"></div>
                <form action="/api/admin/categories/remove/{{$categorie->id}}" method="post" class="col-sm-1 d-flex align-items-center justify-content-center colored">
                    {{ csrf_field() }}
                    <button class="btn btn-outline-info">Remove</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

</div>

@endsection