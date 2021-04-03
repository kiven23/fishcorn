<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
Auth::routes();
 
Route::get('/', 'HomeController@index')->name('home');
Route::get('/api/home',['uses'=>'HomeController@home']);
Route::get('/api/gallery',['uses'=>'HomeController@gallery']);
Route::get('/api/chat',['uses'=>'HomeController@chat']);
Route::get('/test',['uses'=>'HomeController@test']);
Route::get('/api/chat/conversation',['uses'=>'HomeController@conversation']);
Route::get('/api/chat/notify/',['uses'=>'HomeController@notify']);
Route::get('/api/chat/conversation/users',['uses'=>'HomeController@conversationuser']);
Route::get('/api/chat/conversation/receiver/{token}',['uses'=>'HomeController@getSenderinfo']);
Route::post('/api/chat/conversation/create',['uses'=>'HomeController@createconversation']);
Route::post('/api/chat/conversation/updateseen',['uses'=>'HomeController@updateseen']);
Route::post('/api/chat/conversation/chatfunction',['uses'=>'HomeController@chatfunction']);
Route::post('/uploadss', ['uses' => 'ProfileController@uploadapi','as' => 'upload.profile',]);
Route::post('/createprofile',['uses' => 'ProfileController@createprofile','as'=>'create.profile']);