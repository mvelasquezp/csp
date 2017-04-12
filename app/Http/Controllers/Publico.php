<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Hash;
use Mail;
use Redirect;
use Request;
use Response;
use Session;
use Validator;
use App\User as User;

class Publico extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("guest")->except(["logout", "activa_usuario"]);
    }

    public function home() {
        return view("publico.home");
    }

}