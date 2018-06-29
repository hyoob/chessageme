<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){
      return view('pages.home');
    }

    public function getTest(){
      return view('pages.test');
    }

    public function getStatus(){
      return view('pages.status');
    }
}
