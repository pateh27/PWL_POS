<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];
        $activeMenu = 'dashboard';
        return view('welcome', ['breadcrumbs' => $breadcrumbs, 'activeMenu' => $activeMenu]); 
    }
}
