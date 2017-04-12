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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get("/", "Publico@home");

//ajax
Route::group(["prefix" => "ajax", "namespace" => "Ajax"], function() {
	Route::group(["prefix" => "auth"], function() {
		Route::post("login", "Autenticar@autenticar");
	});
	//
});

Route::get("encode/{str}", function($str) {
	return Hash::make($str);
});