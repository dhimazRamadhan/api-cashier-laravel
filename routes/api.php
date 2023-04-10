<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\Controllers\seatsController;
use App\http\Controllers\menusController;
use App\http\Controllers\transactionsController;
use App\http\Controllers\userController;
use App\http\Controllers\authController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify:admin,cashier,manager']], function() {
    Route::get('/user', [AuthController::class, 'getAuthenticatedUser']); //check login
    Route::post('/logout',  [AuthController::class, 'logout']); //logout

});

Route::group([ 'middleware' => ['jwt.verify']],function(){
    Route::group(['middleware' => ['api.manager']], function(){
        Route::get('/getTransactions', [TransactionsController::class, 'index']); //get transactions data
        Route::get('/getDetail/{id}', [TransactionsController::class, 'getDetail']); //look for detail
        Route::post('/filterByStatus', [TransactionsController::class, 'filterByStatus']); //filter by status
        Route::post('/filterByCashier', [TransactionsController::class, 'filterByCashier']); //filter by cashier
        Route::post('/filterByDate', [TransactionsController::class, 'filterByDate']); //filter by date with range
    });

    Route::group(['middleware' => ['api.cashier']], function(){
        Route::get('/getByCashier/{id}', [TransactionsController::class, 'getByCashier']); //get by account
        Route::get('/getAvaibleSeats', [SeatsController::class, 'avaibleSeats']); //cashier seats
        Route::get('/getMenus', [MenusController::class, 'index']); //get menu for order
        Route::post('/addTransactions', [TransactionsController::class, 'store']); //add order
        Route::get('/getDetail/{id}', [TransactionsController::class, 'getDetail']); //look detail
        Route::post('/payment/{id}', [TransactionsController::class, 'payment']); //payment
        Route::post('/filterByStatus', [TransactionsController::class, 'filterByStatus']); //filter by status
    });
    
    Route::group(['middleware' => ['api.admin']], function(){
        //admin seats access
        Route::get('/getSeats', [SeatsController::class, 'index']);
        Route::post('/addSeat', [SeatsController::class, 'store']);
        Route::post('/updateSeat/{id}', [SeatsController::class, 'update']);
        Route::delete('/deleteSeat/{id}', [SeatsController::class, 'destroy']);

        // admin menus access
        Route::get('/getMenus', [MenusController::class, 'index']);
        Route::post('/addMenu', [MenusController::class, 'store']);
        Route::post('/updateMenu/{id}', [MenusController::class, 'update']);
        Route::delete('/deleteMenu/{id}', [MenusController::class, 'destroy']);
        Route::post('/searchMenu', [MenusController::class, 'search']);

        //admin user access
        Route::get('/getUser', [UserController::class, 'index']);
        Route::post('/addUser', [UserController::class, 'store']);
        Route::post('/updateUser/{id}', [UserController::class, 'update']);
        Route::delete('/deleteUser/{id}', [UserController::class, 'destroy']);
        Route::post('/searchUser', [UserController::class, 'search']);
    });
});











