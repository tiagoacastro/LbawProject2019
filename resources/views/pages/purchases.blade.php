@extends('layouts.app')

@section('title', 'Apex Games - Purchase History')

@section('css_sheet')
  <link rel="stylesheet" href="/css/purchases.css">
@endsection

@section('content')
<div class="row purchase_row">
  <div class="col-sm d-flex justify-content-center">
    <h2 class="title">Purchase History</h2>
  </div>
</div>

@if($errors->has('purchases'))

<div class="row purchase_row lastContainer">
  <div class="col-sm-2"></div>
  <div class="col-sm-8 d-flex justify-content-center alert alert-danger">
    <h3><strong>Error: </strong>  {{ $errors->first('purchases') }}</h3>
  </div>
  <div class="col-sm-2"></div>
</div>

@elseif(sizeof($purchases) !== 0)
<div class="row purchase_row">
  <div class="col-sm-2"></div>
  <div class="col-sm-8">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Date</th>
          <th scope="col">Product</th>
          <th scope="col">Paid</th>
          <th scope="col">Payment Method</th>
          <th scope="col">NIF</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchases as $purchase)
          <tr>
            <td>{{date("Y-m-d",strtotime($purchase->purchasedate))}}</td>
            <td>{{$purchase->name}}</td>
            <td>{{$purchase->value}}&euro;</td>
            <td>{{$purchase->method}}</td>
            <td>{{$purchase->nif}}</td>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="col-sm-2"></div>
</div>

<div class="lastContainer">
  <div class="row purchase_row">
    <div class="col-sm-8"></div>
    <div class="col-sm-2 d-flex justify-content-end">
      <a href="{{ route('users.library', [Auth::user()->username]) }}" ><button type="button" class="btn btn-info">View Library</button></a>
    </div>
  </div>
</div>
@else
<div class="row purchase_row lastContainer">
  <div class="col-sm-2"></div>
  <div class="col-sm-8 d-flex justify-content-center">
    <h3>You haven't bought anything yet!</h3>
  </div>
  <div class="col-sm-2"></div>
</div>
@endif

 @endsection