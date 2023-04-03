<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\seats;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SeatsController extends Controller
{
    public function index()
    {
        $posts = Seats::get();
        return response()->json([
            'success' => true,
            'message' => 'Seats data list',
            'data'    => $posts,
            'count'   => count($posts)
        ], 200);
    }

    public function avaibleSeats() //function to get avaible seats for cashier role
    {
        $post = DB::table('seats')
            ->where('status', 'avaible')->get();
        return response()->json([
            'success' => true,
            'message' => 'Seats Data',
            'data'    => $post
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $post = Seats::create([
            'number' => $request->number
        ]);
        if ($post) {
            return response()->json([
                'success' => true,
                'message' => 'Seat data successfully added',
                'data'    => $post
            ], 201);
        }
        return response()->json([
            'success' => false,
            'message' => 'Seat data failed to add'
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'number'      => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $post = Seats::where('id', $id)->update([
            'number'    => $request->number
        ]);
        if ($post) {
            return response()->json([
                'status'  => true,
                'message' => 'Seat data successfully update',
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Seat data failed to update'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $post = DB::table('seats')
            ->where('id', $id)->delete();
        if($post){
            return response()->json([
                'success' => true,
                'message' => 'Seat data successfully deleted'
            ], 200);
        }
        //data post tidak ditemukan
        return response()->json([
            'success' => false,
            'message' => 'Seat data not found'
        ]);
    }
}
