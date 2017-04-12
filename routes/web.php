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

//intranet
Route::get("intranet", "Intranet@intranet");

//procesos principales
Route::group(["prefix" => "intranet"], function() {
	Route::get("sube-diarios", "Intranet@sube_diarios");
	Route::get("genera-articulos-prensa", "Intranet@genera_articulos_prensa");
	Route::get("genera-articulos-web", "Intranet@genera_articulos_web");
	Route::get("genera-articulos-redes", "Intranet@genera_articulos_redes");
	Route::get("genera-articulos-radio", "Intranet@genera_articulos_radio");
	Route::get("genera-articulos-tv", "Intranet@genera_articulos_tv");
	Route::get("boletines", "Intranet@boletines");
	Route::get("busqueda", "Intranet@busqueda");
});

//ajax
Route::group(["prefix" => "ajax", "namespace" => "Ajax"], function() {
	Route::group(["prefix" => "auth"], function() {
		Route::post("login", "Autenticar@autenticar");
	});
	Route::group(["prefix" => "components"], function() {
		Route::post("sube-diarios", "Componentes@sube_diarios");
		Route::post("genera-articulos-prensa", "Componentes@genera_articulos_prensa");
		Route::post("genera-articulos-web", "Componentes@genera_articulos_web");
		Route::post("genera-articulos-redes", "Componentes@genera_articulos_redes");
		Route::post("genera-articulos-radio", "Componentes@genera_articulos_radio");
		Route::post("genera-articulos-tv", "Componentes@genera_articulos_tv");
		Route::post("boletines", "Componentes@boletines");
		//datos adicionales
		Route::post("est_boletin", "Componentes@carga_bloques");
	});
	//subida de datos
	Route::group(["prefix" => "data", "namespace" => "Data"], function() {
		//Archivos
		Route::post("upload_files/{id_medio}", "Archivos@sube_diarios");
		Route::post("img_preview", "Archivos@img_preview");
		Route::post("sv_cortes", "Archivos@sv_cortes");
		Route::post("sv_screenshot", "Archivos@sv_screenshot");
		Route::post("sv_screenshot_rs", "Archivos@sv_screenshot_rs");
		Route::post("sv_audio", "Archivos@sv_audio");
		Route::post("sv_video", "Archivos@sv_video");
		//Combos
		Route::post("ls_cmb_paginas", "Combos@ls_articulo_prensa_cbpaginas");
		//Info
		Route::post("if_audio", "Info@info_audio");
		Route::post("if_video", "Info@info_video");
	});
});

//data
Route::group(["prefix" => "data", "namespace" => "Dhtmlx"], function() {
	Route::group(["prefix" => "grids"], function() {
		Route::get("ls_medios", "Grids@ls_medios");
		Route::get("boletines_dia", "Grids@ls_boletines_dia");
		Route::get("ls_articulos_bloque/{id_boletin}", "Grids@ls_articulos_boletin_bloque");
	});
});

//multimedia
Route::group(["prefix" => "multimedia", "namespace" => "Multimedia"], function() {
	Route::get("get_audio/{fid}/{aid}/{mid}", "Audio@get_audio");
	Route::get("get_video/{fid}/{aid}/{mid}", "Video@get_video");
});

Route::get("encode/{str}", function($str) {
	return Hash::make($str);
});