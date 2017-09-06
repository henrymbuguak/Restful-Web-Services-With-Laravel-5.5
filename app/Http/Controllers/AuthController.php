<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    public function store(Request $request) {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');


        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        if ($user->save()) {
            $user->signin =  [
                'href' => 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            $response = [
                'msg' => 'User created',
                'user' => $user
            ];
            return response()->json($response, 201);
        }

        $response = [
            'msg' => 'An error occurred while creating the user!',
        ];

        return response()->json($response, 404);
    }

    public function signin(Request $request) {
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email','password');

       try {
           if (! $token = JWTAuth::attempt($credentials)) {
               return resolve()->json(['msg' => 'Invalid credetials'], 401);
           }

       } catch (JWTException $e) {
           return resolve()->json(['msg' => 'Unable to create toke'], 500);
       }

        return response()->json(['token' => $token]);
    }
}
