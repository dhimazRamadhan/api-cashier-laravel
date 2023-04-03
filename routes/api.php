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

Route::group([ 'middleware' => ['jwt.verify']],function(){

    Route::group(['middleware' => ['api.manajer']], function(){
    });

    Route::group(['middleware' => ['api.kasir']], function(){
        Route::get('/getAvaibleSeats', [SeatsController::class, 'avaibleSeats']); //cashier seats
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

// admin menus
// Route::get('/getMenus', [MenusController::class, 'index']);
// Route::post('/addMenu', [MenusController::class, 'store']);
// Route::post('/updateMenu/{id}', [MenusController::class, 'update']);
// Route::delete('/deleteMenu/{id}', [MenusController::class, 'destroy']);
// Route::post('/searchMenu', [MenusController::class, 'search']);

//admin user
// Route::get('/getUser', [UserController::class, 'index']);
// Route::post('/addUser', [UserController::class, 'store']);
// Route::post('/updateUser/{id}', [UserController::class, 'update']);
// Route::delete('/deleteUser/{id}', [UserController::class, 'destroy']);
// Route::post('/searchUser', [UserController::class, 'search']);

Route::get('/getTransactions', [TransactionsController::class, 'index']);
Route::get('/getDetail/{id}', [TransactionsController::class, 'getDetail']);
Route::post('/addTransactions', [TransactionsController::class, 'store']);
Route::post('/payment/{id}', [TransactionsController::class, 'payment']);
Route::post('/filterByStatus', [TransactionsController::class, 'filterByStatus']);












