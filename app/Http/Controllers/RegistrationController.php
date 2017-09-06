<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\User;
use JWTAuth;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);


        $message = [
            'msg' => 'User is already registered for this meeting',
            'user' => $user,
            'meeting' => $meeting,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];

        if ($meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        };

        $user->meetings()->attach($meeting);

        /*$meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];*/



        /*$user = [
            'name' => 'Name'
        ]*/;

        $response = [
            'msg' => 'User registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' =>[
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not available'], 404);
        }

        if (!$meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json(['msg' => 'user not registered for meeting, delete not successful'], 401);
        };

        $meeting->users()->detach($user->id);

        $response = [
            'msg' => 'User unregistered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'register' => [
                'href' => 'api/v1/meeting/registration',
                'method' => 'POST',
                'params' => 'user_id, meeting_id'
            ]
        ];

        return response()->json($response, 200);
    }
}
