<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/feeds', [App\Http\Controllers\FeedController::class, 'index'])->name('feeds')->middleware('redirectToLogin');
Route::get('/feed/{_id}', [App\Http\Controllers\FeedController::class, 'getFeed']);
Route::get('/getUserFeeds', [App\Http\Controllers\FeedController::class, 'getUserFeeds']);
Route::post('addFeed', array('uses'=>'App\Http\Controllers\FeedController@create'));
Route::post('/editFeed/{_id}', [App\Http\Controllers\FeedController::class, 'update']);
Route::put('/deleteColumn/{_id}', [App\Http\Controllers\FeedController::class, 'deleteColumn']);
Route::put('/editColumn/{_id}', [App\Http\Controllers\FeedController::class, 'editColumn']);
Route::get('/refreshData/{id}', [App\Http\Controllers\FeedController::class, 'refreshFeedData']);
Route::post('/deleteFeed/{id}', [App\Http\Controllers\FeedController::class, 'delete']);

Route::get('/settings', [App\Http\Controllers\UserController::class, 'showSettings'])->name('settings');

Route::post('/sendToGoogleMerchant', [App\Http\Controllers\FeedController::class, 'sendToGoogleMerchant']);

Route::get('/showAddUser', [App\Http\Controllers\Auth\AddUserToCompany::class, 'showAddUsers']);
Route::post('/addUser', [App\Http\Controllers\Auth\AddUserToCompany::class, 'addUserToCompany']);
Route::post('/deleteUser', [App\Http\Controllers\UserController::class, 'delete']);
