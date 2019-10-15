@extends('layouts.app')

@section('title', 'Apex Games - About Us')

@section('css_sheet')
  <link rel="stylesheet" href="/css/about.css">
@endsection

@section('content')

  <div class="jumbotron jumbotron-fluid about">
    <div class="container">
      <h2 class="display-4">About Us</h2>
    </div>
  </div>

  <div class="container aboutPage">
    <div class="row">
      <div class="col-md-4 vision">
        <div class="TitleAndIcon1">
          <p class="icons"><i class="fas fa-eye"></i></p>
          <h3>Our Vision</h3>
        </div>
        <hr>
        <p> Our vision is to create an efficient platform where players can not only buy amazing
          games, but also share their creations with the world</p>
      </div>

      <div class="col-md-4 story">
        <div class="TitleAndIcon2">
          <p class="icons"><i class="fas fa-scroll"></i></p>
          <h3>Our Story</h3>
        </div>
        <hr>
        <p> Apex Games is an online pc gaming store developed by 4 students of the Integrated Master in Informatics and Computing Engineering at FEUP
          for the class of Database and Web Applications Laboratory (LBAW) </p>
      </div>
      <div class="col-md-4 philosofy3">
        <div class="TitleAndIcon2">
          <p class="icons"><i class="fas fa-yin-yang"></i></p>
          <h3>Our Philosofy</h3>
        </div>
        <hr>
        <p> Gaming really does not only make one happier, but as Nolan Bushnell once
          said "Video Games foster the mindset that allows creativity to grow". So just sit back,
          relax and come and be creative with us! </p>
      </div>
    </div>

    <div class="row">

      <div class="col-md-12 ourTeam">
        <h4>Our Team</h4>
        <div class="row">

          <div class="col-md-6 " id="firstcol">
            <div class="member1">
              <img class="memberPhoto" alt="member profile picture" src="{{asset('img/helena.PNG')}}">
              <div class="member">
                <h5 class="memberName">Helena Montenegro</h5>
                <div class="memberData">
                  <a href="mailto:up201604184@fe.up.pt" > <i class="fas fa-envelope"></i></a>
                  <a href="https://github.com/helenaMontenegro" > <i class="fab fa-github"></i> </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6" id="secondcol">
            <div class="member2">
              <img class="memberPhoto" alt="member profile picture" src="{{asset('img/joana.PNG')}}">
              <div class="member">
                <h5 class="memberName">Joana Silva</h5>
                <div class="memberData">
                  <a href="mailto:up201208979@fe.up.pt" > <i class="fas fa-envelope"></i></a>
                  <a href="https://github.com/jmcsilva98" > <i class="fab fa-github"></i> </a>
                </div>
              </div>
            </div>
          </div>



        </div>
        <div class="row">

          <div class="col-md-6 " id="thirdcol">
            <div class="member3">
              <img class="memberPhoto" alt="member profile picture" src="{{asset('img/castro.PNG')}}">
              <div class="member">
                <h5 class="memberName">Tiago Castro</h5>
                <div class="memberData">
                  <a href="mailto:up201606186@fe.up.pt" > <i class="fas fa-envelope"></i></a>
                  <a href="https://github.com/tiagoacastro" > <i class="fab fa-github"></i> </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6" id="fourthcol">
            <div class="member4">
              <img class="memberPhoto" alt="member profile picture" src="{{asset('img/pedro.PNG')}}">
              <div class="member">
                <h5 class="memberName">João Pedro Franco</h5>
                <div class="memberData">
                  <a href="mailto:up201604828@fe.up.pt" > <i class="fas fa-envelope"></i></a>
                  <a href="https://github.com/pedrofixe" > <i class="fab fa-github"></i> </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection
