<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role' => 'required',
            'username' => 'required',
            'password' => 'required|string|min:6'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'role' => $request->get('role'),
            'username' => $request->get('username'),
            'password' => Hash::make($request->get('password')),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201); 
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username','password');
        try {
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json([
                    'error' => "Username or password doesn't match"
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed generate token'
            , 500]);
        }
            $user = JWTAuth::user();
            return response()->json([
                'message' => 'Login successfully',
                'token' => $token,
                'user' => $user
            ]);
    }

    public function getAuthenticatedUser() {
        try{
            if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
            } 
        }
        catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode()); }
        catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode()); }
        catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user')); 
    }

    public function logout(Request $request)
    {
		if(JWTAuth::invalidate(JWTAuth::getToken())) {
			return response()->json(['message' => 'Logout successfully']);
        } else {
            return response()->json(['message' => 'Failed logout']);
        }
    }
}

