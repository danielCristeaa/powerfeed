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
Route::get('/feeds', [App\Http\Controllers\FeedController::class, 'index'])->name('feeds');
Route::get('/feed/{_id}', [App\Http\Controllers\FeedController::class, 'getFeed']);
Route::get('/getUserFeeds', [App\Http\Controllers\FeedController::class, 'getUserFeeds']);
Route::post('addFeed', array('uses'=>'App\Http\Controllers\FeedController@create'));
Route::post('/editFeed/{_id}', [App\Http\Controllers\FeedController::class, 'update']);
Route::post('/renameColumn/{_id}', [App\Http\Controllers\FeedController::class, 'renameColumn']);
Route::put('/deleteColumn/{_id}', [App\Http\Controllers\FeedController::class, 'deleteColumn']);
