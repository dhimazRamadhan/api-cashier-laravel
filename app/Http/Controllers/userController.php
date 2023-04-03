<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\user;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $posts = User::get();
        return response()->json([
            'success' => true,
            'message' => 'User data list',
            'data'    => $posts,
            'count'   => count($posts)
        ], 200);
    }

    public function store(Request $request)
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
        $post = User::create([
            'name' => $request->get('name'),
            'role' => $request->get('role'),
            'username' => $request->get('username'),
            'password' => Hash::make($request->get('password')),
        ]); 
        if ($post) {
            return response()->json([
                'status'  => true,
                'message' => 'User successfully added',
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'User failed to add'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'role'     => 'required',
            'username' => 'required',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $post = User::where('id', $id)->update([
            'name'      => $request->name,
            'role'      => $request->role,
            'username'  => $request->username,
            'password'  => Hash::make($request->get('password'))
        ]);
        if ($post) {
            return response()->json([
                'status'  => true,
                'message' => 'User successfully updated',
                'data'    => $post
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'User failed to update'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $post = DB::table('user')
            ->where('id', $id)->delete();
        if($post){
            return response()->json([
                'success' => true,
                'message' => 'User successfully deleted'
            ], 200);
        }
        //data post tidak ditemukan
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ]);
    }

    public function logout(Request $request)
    {
		if(JWTAuth::invalidate(JWTAuth::getToken())) {
			return response()->json(['message' => 'Success to logout!']);
        } else {
            return response()->json(['message' => 'Failed to logout!']);
        }
    }

    public function search(Request $request){
        $keyword = $request->input('keyword');
        $result = User::where('name','LIKE','%'.$request->keyword.'%')
            ->orWhere('username','LIKE','%'.$request->keyword.'%')
            ->get();
        if(count($result)){
            return Response()->json(['data' => $result, 'count' => count($result)]);
        }else{
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }            
    }

    public function getCashier() { //filtering by cashier for manager role
        $post = User::where('role','cashier')
            ->get();
        return response()->json([
            'data' => $post
        ]);
    }
}

