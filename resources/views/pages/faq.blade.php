@extends('layouts.app')

@section('title', 'Apex Games - FAQ')

@section('css_sheet')
  <link rel="stylesheet" href="/css/faq.css">
@endsection

@section('content')

   <div id="faq_content">
        <div class="row faq_row">
            <div class="col-sm-2"></div>
            <div class="col-sm px-0 faq_title">
                <h3>Frequently Asked Questions</h3>
            </div>
        </div>

        <div class="row question">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 rounded colored">
                <h5>Do you have a physical store?</h5>
                <p>No, we don't have a physical location.</p>
            </div>
        </div>

        <div class="row question">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 rounded colored">
                <h5>What happens to my information when I delete my account?</h5>
                <p>The information relative to the purchases made in the shop, as well as the username and email associated to the user's account, remain in the database after deleting the account. The reviews and score submitted by the user stay in the website but become anonymous. </p>
            </div>
        </div>

        <div class="row question">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 rounded colored">
                <h5>What are the methods of payment that I can use to make purchases in the shop?</h5>
                <p>The only method of payment available is Paypal.</p>
            </div>
        </div>

        <div class="row question">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 rounded colored">
                <h5>How can I get a game after buying it?</h5>
                <p>After buying the game, it'll become available for download in the user's library.</p>
            </div>
        </div>

        <div class="row question">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 rounded colored">
                <h5>Can I cancel a purchase?</h5>
                <p>After payment, it's impossible to cancel the purchase, since the game becomes automatically available in the user's library.</p>
            </div>
        </div>

    </div>
@endsection
