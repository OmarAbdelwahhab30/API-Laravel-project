<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SanctumController extends Controller
{

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // create token
        $token = $user->createToken("personal access token")->plainTextToken;
        $user->token = $token;
        return response()->json(['user' => $user]);
    }


    public function login(Request $request){
        $credentials = $request->only('name', 'password');
        if (Auth::attempt($credentials)){
            $user = User::where("name",$request->name)->first();
            $token = $user->createToken("personal access token")->plainTextToken;
            $user->token = $token;
            return response()->json(["user"=>$user]);
        }
        return response()->json(["user"=> "These credentials do not match our records."]);
    }


    public function logout(Request $request){
        if ($request->user()->currentAccessToken()->delete()){
            return response()->json(['msg' => "You have been successfully logged out!"]);
        }
        return response()->json(['msg' => "some thing went wrong"]);
    }
}
