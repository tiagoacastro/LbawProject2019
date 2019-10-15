@extends('layouts.app')

@section('title', 'Apex Games - Admin Sales')

@section('css_sheet')
  <link rel="stylesheet" href="/css/admin_sales.css">
@endsection

@section('content')

<div class="col-sm-2 sidebar">
  <ul class="nav flex-column change-flex-mobile">
    <li class="nav-item">
      <a class="nav-link active" href="/admin/sales"><i class="fas fa-home"></i> <span class="menu_item">Game sales</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/pending"><i class="fas fa-gamepad"></i> <span class="menu_item">Pending games</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/users"><i class="fas fa-users"></i> <span class="menu_item">Search user</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/categories"><i class="fas fa-list-ul"></i> <span class="menu_item">Manage categories</span></a>
    </li>
  </ul>
</div>

<main class="col-md-9 col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <h1 id="title">Game sales</h1>
  </div>

  <div class="table-responsive d-flex justify-content-center">
    <table id="table" class="table table-sm table-bordered">
      <thead class="">
        <th scope="col" style="width: 20%">Date</th>
        <th scope="col" style="width: 20%">Username</th>
        <th scope="col" style="width: 20%">Game</th>
        <th scope="col" style="width: 20%">Price</th>
        <th scope="col" style="width: 20%">Payment method</th>
      </thead>
      <tbody>
        @foreach($sales as $sale)
          <tr>
            <td>{{date("d-m-Y", time($sale->purchasedate))}}</td>
            <td>{{$sale->username}}</td>
            <td>{{$sale->name}}</td>
            <td>{{$sale->value}}</td>
            <td>{{$sale->method}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</main>


@endsection
