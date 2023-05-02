<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function user_profile(){
        
        return Inertia::render("UserProfile");
    }

    public function dashboard(){
        
        return Inertia::render("Dashboard");
    }
}
