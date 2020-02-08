<?php

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

Route::get('/', 'LoginController@index');

Route::post('/user', 'LoginController@store');

Route::get('/chat', 'ChatController@index');

Route::post('/chat', 'ChatController@store');

Route::get('/chat/private/{user}', 'ChatController@privateChat');

Route::get('/chat/messages/private/{user}', 'ChatController@privateMessages');

Route::get('/chat/messages/{type}', 'ChatController@messages');
Route::get('/chat/recipients', 'ChatController@recipients');

Route::post('/signout', 'ChatController@signout');
