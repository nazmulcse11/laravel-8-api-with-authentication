<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//get api for fetch single users
Route::get('/users/{id?}', [ApiController::class, 'users']);

//secure get api for fetch users 
Route::get('/users-list', [ApiController::class, 'usersList']);

//post api for add single user
Route::post('/add-users', [ApiController::class, 'addUsers']);

//register api to add single user with api token
Route::post('/register-users', [ApiController::class, 'registerUsers']);

//user login api /update token
Route::post('/login-users', [ApiController::class, 'loginUsers']);

//user logout api /update token
Route::post('/logout-users', [ApiController::class, 'logoutUsers']);

//post api for add multiple user
Route::post('/add-multiple-users', [ApiController::class, 'addMultipleUsers']);

//put api for update one or more records
Route::put('/update-user-details/{id}', [ApiController::class, 'updateUserDetails']);

//patch api for update single records
Route::patch('/update-user-name/{id}', [ApiController::class, 'updateUserName']);

//delete user with param
Route::delete('/delete-user/{id}', [ApiController::class, 'deleteUser']);

//delete user with json
Route::delete('/delete-user-with-json', [ApiController::class, 'deleteUserWithJson']);

//delete multiple user with param
Route::delete('/delete-multiple-user/{ids}', [ApiController::class, 'deleteMultipleUser']);

//delete multiple user with json
Route::delete('/delete-multiple-user-with-json', [ApiController::class, 'deleteMultipleUserWithJson']);


//register user with passport
Route::post('/register-user-with-passport',[ApiController::class,'registerWithPassport']);
