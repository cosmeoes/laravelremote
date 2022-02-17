<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        return view('home', ['jobPosts' => JobPost::orderBy('source_created_at', 'desc')->get()]);
    }
}
