<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(Request $request) {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        return "It works";
    }

    public function signin(Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');
        return "It works!";
    }
}
