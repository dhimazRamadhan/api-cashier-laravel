<?php

namespace App\Http\Controllers;
use App\Models\transactions;
use App\Models\detailstransactions;
use App\Models\menus;
use App\Models\seats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class transactionsController extends Controller
{

    public function getDetail($id)
    {
        $posts = DB::table('details_transactions')
            ->where('transaction_id', $id)
            ->join('menus','details_transactions.menu_id','=','menus.id')
            ->get();
        $total = DB::select("SELECT transaction_id, SUM(subtotal) as 'total' from details_transactions WHERE transaction_id = $id GROUP BY transaction_id");
        print_r($total);
        $total_int = intval($total[0]->total);
        return response()->json([
            'success' => true,
            'count' => count($posts),
            'message' => 'Detail Transactions list',
            'data' => $posts,
            'total' => $total_int
        ], 200);
    }
    
    public function index()
    {
        $posts = DB::table('transactions')
                ->select('transactions.*','user.name as cashier')
                ->join('user','transactions.user_id','=','user.id')
                ->get();
        return response()->json([
            'success' => true,
            'count' => count($posts),
            'message' => 'Transactions data list',
            'data' => $posts,
            'count'   => count($posts)
        ], 200);
    }

    public function getByCashier($id)
    {
        $post = DB::table('transactions')
            ->select('transactions.*','user.id as userId','user.name')
            ->where('user_id',$id)
            ->join('user','transactions.user_id','=','user.id')
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Transactions data list',
            'data'    => $post,
            'count'   => count($post)
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'seat_id' => 'required',
            'customer_name' => 'required',       
        ]);
        //get seat status
        $get_seat = DB::table('seats')->where('id', $request->seat_id)->get();
        
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        } 
        else if ($get_seat[0]->status == 'not avaible'){
            return response()->json(['success' => false, 'message' => 'Seat not avaible']);
            exit;
        }

        // update seat status if its filled
        Seats::where('id', $request->seat_id)->update([
            'status' => 'not avaible'
        ]);

        $transaction = new transactions();
		$transaction->transaction_date = now()->format('Y-m-d');
        $transaction->user_id = $request->user_id;
        $transaction->seat_id = $request->seat_id;
        $transaction->customer_name = $request->customer_name;
        $transaction->status = "unpaid";
		$transaction->save();
        
        for($i = 0; $i < count($request->details); $i++){
            $detail_transactions = new detailsTransactions();
            $detail_transactions->transaction_id = $transaction->id;
            $detail_transactions->menu_id = $request->details[$i]['menu_id'];
            $detail_transactions->qty = $request->details[$i]['qty'];
            //get menu for price
            $menu = Menus::where('id', '=', $detail_transactions->menu_id)->first();
            $price = $menu->price;
            $detail_transactions->subtotal = $request->details[$i]['qty'] * $price;
            $detail_transactions->save();
        }
        $details = detailsTransactions::where('transaction_id', '=', $detail_transactions->transaction_id)->get();
        return response()->json([
            'data' => $transaction,
            'details' => $details,
        ]);
    }

    public function payment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cash' => 'required',    
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        //get total
        $total = DB::select("SELECT transaction_id, SUM(subtotal) as 'total' from details_transactions WHERE transaction_id = $id GROUP BY transaction_id");
        $total_int = intval($total[0]->total);

        //check change
        $change = $request->cash - $total_int;

        if ($request->cash < $total_int) {
            return response()->json(['status' => false, 'message' => 'Not enough cash']);
            exit;
        }

        $update_bayar = transactions::where('id', $id)->update([
            'status' => 'paid'
        ]);

        $get_seat = DB::table('transactions')->where('id', $id)->get(); //get seat status

        Seats::where('id', $get_seat[0]->seat_id)->update([
            'status' => 'avaible'
        ]);

        return response()->json([
            'message' => 'Payment successfully',
            'total' => $total_int,
            'cash' => $request->cash,
            'change' => $change
        ]); 
    }

    public function filterByDate(Request $request) {
        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',       
        ]);
        $post = Transactions::whereBetween('transaction_date', [$request->start, $request->end])
            ->get();
        return response()->json([
            'data' => $post
        ]);
    }

    public function FilterByCashier(Request $request) {
        $validator = Validator::make($request->all(), [
            'param' => 'required'            
        ]);
        $post = Transactions::where('user_id', $request->param)
                ->get();
        return response()->json([
            'data' => $post
        ]);
    }

    public function FilterByStatus(Request $request) {
        $validator = Validator::make($request->all(), [
            'status' => 'required'            
        ]);
        $post = Transactions::where('status', $request->status)
            ->get();
        return response()->json([
            'data' => $post
        ]);
    }
}
