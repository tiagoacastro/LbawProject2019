<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticController extends Controller
{
 	public function about() {
 		return view('pages.about');
 	}

 	public function faq() {
 		return view('pages.faq');
 	}

 	public function error404() {
 		return view('pages.error404');
 	}
}
