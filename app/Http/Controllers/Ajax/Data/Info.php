<?php

namespace App\Http\Controllers\Ajax\Data;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Info extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function info_audio() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($fid,$mid,$aid)) {
                $audio = DB::table("csp_articulo as art")
                    ->join("csp_articulo_multimedia as aml", function($join) {
                        $join->on("art.id_articulo", "=", "aml.id_articulo")
                            ->on("art.id_empresa", "=", "aml.id_empresa");
                    })
                    ->join("csp_medio as med", function($join2) {
                        $join2->on("med.id_medio", "=", "art.id_medio")
                            ->on("med.id_empresa", "=", "art.id_empresa");
                    })
                    ->where("aml.id_archivo", $fid)
                    ->where("art.id_articulo", $aid)
                    ->where("art.id_medio", $mid)
                    ->select("med.des_nombre as medio", "art.des_nombre as articulo", "aml.des_nombre_archivo as archivo", "aml.des_inicio as inicio");
                if($audio->count() > 0) return Response::json([
                    "success" => true,
                    "prop" => $audio->first(),
                    "source" => url("multimedia/get_audio", [$fid, $aid, $mid])
                ]);
                else return Response::json([
                    "success" => false,
                    "message" => "Archivo incorrecto"
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

    public function info_video() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($fid,$mid,$aid)) {
                $audio = DB::table("csp_articulo as art")
                    ->join("csp_articulo_multimedia as aml", function($join) {
                        $join->on("art.id_articulo", "=", "aml.id_articulo")
                            ->on("art.id_empresa", "=", "aml.id_empresa");
                    })
                    ->join("csp_medio as med", function($join2) {
                        $join2->on("med.id_medio", "=", "art.id_medio")
                            ->on("med.id_empresa", "=", "art.id_empresa");
                    })
                    ->where("aml.id_archivo", $fid)
                    ->where("art.id_articulo", $aid)
                    ->where("art.id_medio", $mid)
                    ->select("med.des_nombre as medio", "art.des_nombre as articulo", "aml.des_nombre_archivo as archivo", "aml.des_inicio as inicio");
                if($audio->count() > 0) return Response::json([
                    "success" => true,
                    "prop" => $audio->first(),
                    "source" => url("multimedia/get_video", [$fid, $aid, $mid])
                ]);
                else return Response::json([
                    "success" => false,
                    "message" => "Archivo incorrecto"
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