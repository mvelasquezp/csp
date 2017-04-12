<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Redirect;
use App\User as User;

class Intranet extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function intranet() {
        return view("intranet.home");
    }

    public function sube_diarios() {
        return view("intranet.subediarios");
    }

    public function genera_articulos_prensa() {
        return view("intranet.generarticulos_prensa");
    }

    public function genera_articulos_web() {
        return view("intranet.generarticulos_web");
    }

    public function genera_articulos_redes() {
        return view("intranet.generarticulos_redes");
    }

    public function genera_articulos_radio() {
        return view("intranet.generarticulos_radio");
    }

    public function genera_articulos_tv() {
        return view("intranet.generarticulos_tv");
    }

    public function boletines() {
        return view("intranet.boletines");
    }

    public function busqueda() {
        return view("intranet.busqueda");
    }

}