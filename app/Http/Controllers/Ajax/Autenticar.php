<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Hash;
use Mail;
use Request;
use Response;
use Validator;
use App\User as User;

class Autenticar extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("guest")->except(["logout", "activa_usuario"]);
    }

    public function autenticar() {
        if(Request::ajax()) {
            $inputs = Request::input();
            $rules = ["user" => "required", "pswd" => "required"];
            $v = Validator::make($inputs, $rules);
            if($v->passes()) {
                if(Auth::attempt(["des_alias" => $inputs["user"], "password" => $inputs["pswd"]], true)) {
                    return Response::json(["success" => true, "message" => "Bienvenido, " . $inputs["user"]]);
                }
                else {
                    return Response::json(["success" => false, "message" => "Credenciales incorrectas"]);
                }
            }
            else {
                return Response::json(["success" => false, "message" => "Los parámetros son incorrectos"]);
            }
        }
        else {
            return Response::json(["success" => false, "message" => "No tiene permisos para acceder aquí"]);
        }
    }

}