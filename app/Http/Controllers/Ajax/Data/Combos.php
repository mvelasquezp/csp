<?php

namespace App\Http\Controllers\Ajax\Data;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Combos extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function ls_articulo_prensa_cbpaginas() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($mid)) {
                $id_medio = $mid;
                $user = Auth::user();
                if(DB::table("csp_medio")->where("id_medio", $id_medio)->count() > 0) {
                    $hoy = explode("-", date("Y-m-d"));
                    $dir_medio = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], $id_medio]);
                    if(file_exists($dir_medio)) {
                        $files = scandir($dir_medio);
                        $data = [];
                        foreach ($files as $key => $file) {
                            if($key > 1) array_push($data, ["value" => $file, "text" => "Página " . str_replace(".jpg", "", $file)]);
                        }
                        return Response::json([
                            "success" => true,
                            "data" => $data
                        ]);
                    }
                    else return Response::json([
                        "success" => true,
                        "data" => [
                            ["value" => 0, "text" => "No hay páginas disponibles"]
                        ]
                    ]);
                }
                else return Response::json([
                    "success" => false,
                    "message" => "No existe el medio especificado"
                ]);
            }
            else return Response::json([
                "success" => false,
                "message" => "Parámetros incorrectos"
            ]);
        }
        else return Response::json([
        	"success" => false,
        	"message" => "No tiene permisos para acceder aquí"
    	]);
    }

}