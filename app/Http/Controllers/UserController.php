<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Regiter new user.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function registration(Request $data)
    {
        $user = User::where('email', '=', $data['email'])->select('id')->first();

        if($user) {
            return response()->json([ 'status'=> 0, 'error' => 'User already registered' ]);
        }

        if( !$data['email'] || !$data['password']) {
            return response()->json(['status'=> 0, 'error' => 'Some fields are empty']);
        }

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'api_token' => Str::random(60)
        ]);

        return response()->json([ 'status'=> 1, 'data'=> [ 'token' => $user->api_token] ]);
    }

    /**
     * Get user details.
     *
     * @param  string $token
     * @return \Illuminate\Http\Response
     */

    public function getUserDetails(string $token)
    {
        $user = User::where('api_token', '=', $token)->select('email')->first();
        return response()->json([ $user->email ]);
    }
}
