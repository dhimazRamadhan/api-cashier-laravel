<?php

namespace App\Http\Controllers;
use App\Models\menus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class menusController extends Controller
{
    public function index()
    {
        $posts = Menus::get();
        return response()->json([
            'success' => true,
            'message' => 'Menus data list',
            'data'    => $posts,
            'count'   => count($posts)
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'price' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $post = Menus::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'image' => $request->file('image')->store('menu-image'),
            'price' => $request->price,
        ]);
        if ($post) {
            return response()->json([
                'success' => true,
                'message' => 'Menu data successfully added',
                'data'    => $post
            ], 201);
        }
        return response()->json([
            'success' => false,
            'message' => 'Menu data failed to add'
        ]);
    }

    public function update(Request $request, $id)
    {    
        $validator = Validator::make($request->all(), [      
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'price' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } 
        $get_item = DB::table('menus')
            ->where('id', $id)->first();
        if ($request->file('image')) {
            unlink('storage/'.$get_item->image);
        }    
        $post = menu::where('id', $id)->update([
            'name'      => $request->name,
            'type'     => $request->type, 
            'description' => $request->description,
            'image'    => $request->file('image')->store('menu-image'), 
            'price'     => $request->price
        ]);
        if ($post) {
            return response()->json([
                'status'  => true,
                'message' => 'Menu succcessfully updated',
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update menu'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $get_item = DB::table('menus')
            ->where('id', $id)->first();   
        $post = DB::table('menus')
            ->where('id', $id)->delete();
        if($post){
            unlink('storage/'.$get_item->image);
            return response()->json([
                'success' => true,
                'message' => 'Menu successfully deleted'
            ], 200);
        }
        //data not found
        return response()->json([
            'success' => false,
            'message' => 'Menu not found'
        ]);
    }

    public function search(Request $request){
        $keyword = $request->input('keyword');
        $result = Menus::where('name','LIKE','%'.$request->keyword.'%')
            ->orWhere('description','LIKE','%'.$request->keyword.'%')
            ->get();
        if(count($result)){
            return Response()->json(['data' => $result,'count' => count($result)
        ]);
        }else{
            return response()->json([
                'message' => 'Data not found    '
            ], 404);
        }            
    }
}
