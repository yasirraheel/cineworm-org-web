<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Award;
use Illuminate\Http\Request;

class AwardsController extends Controller
{
    public function index()
    {
        $awards = Award::with(['user', 'movie'])->orderBy('id', 'desc')->paginate(15);
        
        return view('admin.pages.awards.list', compact('awards'));
    }
}
