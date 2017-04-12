<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Componentes extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function sube_diarios() {
        if(Request::ajax()) {
            $medios = DB::table("csp_medio")
            	->where("st_medio", 15)
            	->where("tp_medio", 10)
            	->select("id_medio as value", "des_nombre as text")
            	->get();
        	return Response::json([
        		"success" => true,
        		"data" => [
        			"cbmedio" => $medios
        		]
    		]);
        }
        else return Response::json([
        	"success" => false,
        	"message" => "No tiene permisos para acceder aquí"
    	]);
    }

	//creacion articulos - prensa

    public function genera_articulos_prensa() {
        if(Request::ajax()) {
            $medios = DB::table("csp_medio")
            	->where("st_medio", 15)
            	->where("tp_medio", 10)
            	->select("id_medio as value", "des_nombre as text")
            	->get();
        	return Response::json([
        		"success" => true,
        		"data" => [
        			"cbmedio" => $medios,
        			"ics_path" => url("images")
        		]
    		]);
        }
        else return Response::json([
        	"success" => false,
        	"message" => "No tiene permisos para acceder aquí"
    	]);
    }

	public function genera_articulos_web() {
		if(Request::ajax()) {
            $medios = DB::table("csp_medio")
            	->where("st_medio", 15)
            	->where("tp_medio", 11)
            	->select("id_medio as value", "des_nombre as text")
            	->get();
        	return Response::json([
        		"success" => true,
        		"data" => [
        			"cbmedio" => $medios
        		]
    		]);
        }
        else return Response::json([
        	"success" => false,
        	"message" => "No tiene permisos para acceder aquí"
    	]);
	}

    public function genera_articulos_redes() {
        if(Request::ajax()) {
            $medios = DB::table("csp_medio")
                ->where("st_medio", 15)
                ->where("tp_medio", 12)
                ->select("id_medio as value", "des_nombre as text")
                ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "cbmedio" => $medios
                ]
            ]);
        }
        else return Response::json([
            "success" => false,
            "message" => "No tiene permisos para acceder aquí"
        ]);
    }

    public function genera_articulos_radio() {
        if(Request::ajax()) {
            $medios = DB::table("csp_medio")
                ->where("st_medio", 15)
                ->where("tp_medio", 13)
                ->select("id_medio as value", "des_nombre as text")
                ->get();
            $audios = DB::table("csp_articulo as art")
                ->join("csp_articulo_multimedia as aml", function($join) {
                    $join->on("art.id_articulo", "=", "aml.id_articulo")
                        ->on("art.id_medio", "=", "aml.id_medio")
                        ->on("art.id_empresa", "=", "aml.id_empresa");
                })
                ->join("csp_medio as med", function($join2) {
                    $join2->on("art.id_medio", "=", "med.id_medio")
                        ->on("art.id_empresa", "=", "med.id_empresa");
                })
                ->whereDate("art.created_at", date("Y-m-d"))
                ->where("aml.tp_archivo", 20)
                ->select("art.des_nombre as nom", "art.id_articulo as aid", "art.id_medio as mid" ,"aml.id_archivo as fid" ,"med.des_nombre as nmd", DB::raw("date_format(art.created_at, '%H:%i:%s') as fup"))
            ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "cbmedio" => $medios,
                    "ics_path" => url("images"),
                    "files" => $audios
                ]
            ]);
        }
        else return Response::json([
            "success" => false,
            "message" => "No tiene permisos para acceder aquí"
        ]);
    }

    public function genera_articulos_tv() {
        if(Request::ajax()) {
            $medios = DB::table("csp_medio")
                ->where("st_medio", 15)
                ->where("tp_medio", 14)
                ->select("id_medio as value", "des_nombre as text")
                ->get();
            $videos = DB::table("csp_articulo as art")
                ->join("csp_articulo_multimedia as aml", function($join) {
                    $join->on("art.id_articulo", "=", "aml.id_articulo")
                        ->on("art.id_medio", "=", "aml.id_medio")
                        ->on("art.id_empresa", "=", "aml.id_empresa");
                })
                ->join("csp_medio as med", function($join2) {
                    $join2->on("art.id_medio", "=", "med.id_medio")
                        ->on("art.id_empresa", "=", "med.id_empresa");
                })
                ->whereDate("art.created_at", date("Y-m-d"))
                ->where("aml.tp_archivo", 21)
                ->select("art.des_nombre as nom", "art.id_articulo as aid", "art.id_medio as mid" ,"aml.id_archivo as fid" ,"med.des_nombre as nmd", DB::raw("date_format(art.created_at, '%H:%i:%s') as fup"))
            ->get();
            return Response::json([
                "success" => true,
                "data" => [
                    "cbmedio" => $medios,
                    "ics_path" => url("images"),
                    "files" => $videos
                ]
            ]);
        }
        else return Response::json([
            "success" => false,
            "message" => "No tiene permisos para acceder aquí"
        ]);
    }

    public function boletines() {
        if(Request::ajax()) {
            return Response::json([
                "success" => true,
                "data" => [
                    "ics_path" => url("images/icons") . "/"
                ]
            ]);
        }
        else return Response::json([
            "success" => false,
            "message" => "No tiene permisos para acceder aquí"
        ]);
    }

    public function carga_bloques() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($bid)) {
                $usuario = Auth::user();
                $boletin = DB::table("csp_boletin_catalogo as cat")
                    ->join("csp_boletin_cabecera as cab", function($join) {
                        $join->on("cat.id_boletin", "=", "cab.id_boletin")
                            ->on("cat.cod_cliente", "=", "cab.cod_cliente")
                            ->on("cat.id_empresa", "=", "cab.id_empresa");
                    })
                    ->where("cat.id_empresa", $usuario->id_empresa)
                    ->where("cat.id_boletin", $bid)
                            ->whereDate("cab.fe_programacion", date("Y-m-d"));
                if($boletin->count() > 0) {
                    $boletin = $boletin->select("cat.id_boletin as id", "cat.tp_boletin as tpo", "cat.cod_cliente as cli", "cat.des_nombre as nom")->first();
                    $bloques = [];
                    $num_bloques = 0;
                    $xml_file_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $usuario->id_empresa, "boletines", $boletin->cli, $boletin->id . ".xml"]);
                    if(file_exists($xml_file_path)) {
                        $xml_bloques = simplexml_load_file($xml_file_path);
                        foreach($xml_bloques->bloque as $bloque) {
                            unset($bloque->keywords);
                            array_push($bloques, $bloque);
                            $num_bloques++;
                        }
                        return Response::json([
                            "success" => true,
                            "num" => $num_bloques,
                            "struct" => $bloques
                        ]);
                    }
                    else return Response::json([
                        "success" => false,
                        "message" => "Error al cargar estructura del boletin"
                    ]);
                }
                else return Response::json([
                    "success" => false,
                    "message" => "El boletín especificado no existe"
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