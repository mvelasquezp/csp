<?php

namespace App\Http\Controllers\Ajax\Data;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Archivos extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function sube_diarios($id_medio) {
        if(Request::ajax()) {
        	$user = Auth::user();
            $file = $_FILES["file"];//744
            $date = explode("-", date("Y-m-d"));
            $files_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "diarios", $date[0], $date[1], $date[2], $id_medio]);
            @mkdir($files_path, "777", true);
            $tmp_name = $file["tmp_name"];
            $real_name = $file["name"];
            $file_type = $file["type"];
            if(in_array($file_type, ["image/png", "image/jpeg"])) { //es imagen
            	$dest_path = $files_path . DIRECTORY_SEPARATOR . $real_name;
				if(rename($tmp_name, $dest_path)) {
					@file_put_contents("D:\\files\\log_" . $real_name . ".txt", print_r($file, true));
	        		return Response::json([
		    			"state" => true,
		    			"name" => $real_name,
		    			"extra" => []
					]);
				}
	    		else return Response::json([
	    			"state" => false,
	    			"message" => "Ocurrió un error al mover el archivo hacia el directorio correspondiente"
				]);
            }
        	else if(strcmp($file_type, "application/octet-stream") == 0) {
        		//descomprime y mueve
        		$dest_path = $files_path . DIRECTORY_SEPARATOR . $real_name;
        		rename($tmp_name, $dest_path);
        		return Response::json([
	    			"state" => true,
	    			"name" => $rename
				]);
        	}
        	else return Response::json([
    			"state" => false,
    			"message" => "Tipo de archivo inválido"
			]);
        }
        else return Response::json([
        	"state" => false,
        	"message" => "No tiene permisos para acceder aquí"
    	]);
    }

    public function img_preview() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($mid, $pid, $sh)) {
                $hoy = explode("-", date("Y-m-d"));
                $img_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], $mid, $pid]);
                if(file_exists($img_path)) {
                    $size = getimagesize($img_path);
                        $factor_escala = round($size[0] / $sh, 4);
                    $image = imagecreatefromjpeg($img_path);
                    $articulos = DB::table("csp_articulo_multimedia")
                        ->where("id_medio", $mid)
                        ->where("id_empresa", $user->id_empresa)
                        ->where("des_nombre_archivo", $pid)
                        ->whereDate("created_at", date("Y-m-d"))
                        ->select("id_archivo as aid", "nu_xi as xi", "nu_yi as yi", "nu_xf as xf", "nu_yf as yf")
                    ->get();
                    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 63);
                    foreach($articulos as $ar) {
                        imagefilledrectangle($image, $ar->xi, $ar->yi, $ar->xf, $ar->yf, $alpha);
                    }
                    $image = imagescale($image, $sh);
                    ob_start();
                        imagejpeg($image);
                        $contents = ob_get_contents();
                    ob_end_clean();
                    $arr_cortes = [];
                    $clips = DB::table("csp_articulo as art")
                        ->join("csp_articulo_multimedia as cts", function($join) {
                            $join->on("art.id_articulo", "=", "cts.id_articulo")
                                ->on("art.id_medio", "=", "cts.id_medio")
                                ->on("art.id_empresa", "=", "cts.id_empresa");
                        })
                        ->where("art.id_medio", $mid)
                        ->where("art.id_empresa", $user->id_empresa)
                        ->where("cts.des_nombre_archivo", $pid)
                        ->whereDate("art.created_at", date("Y-m-d"))
                        ->select("art.id_articulo as aid", "art.des_seccion as scn", "art.des_nombre as nom", DB::raw("count(cts.id_archivo) as num"))
                        ->groupBy("art.id_articulo", "art.des_seccion", "art.des_nombre")
                        ->get();
                    foreach($clips as $clip) array_push($arr_cortes, $clip);
                    return Response::json(["success" => true, "escala" => $factor_escala, "imgdata" => "data:image/jpeg;base64," . base64_encode($contents), "cortes" => $arr_cortes]);
                }
                else return Response::json([
                    "success" => false,
                    "message" => "La imagen especificada no existe"
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

    public function sv_cortes() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($mid, $pid, $scc, $ttl, $cortes, $sx) && count($cortes) > 0) {
                //crea el articulo y captura el id
                $id_articulo = DB::table("csp_articulo")->insertGetId([
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre" => $ttl,
                    "des_seccion" => $scc,
                    "cod_usuario_registra" => $user->cod_usuario
                ]);
                //prepara el directorio
                $hoy = explode("-", date("Y-m-d"));
                $img_dir = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], "cortes"]);
                @mkdir($img_dir, "777", true);
                //carga la imagen principal
                $original_dir = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], $mid, $pid]);
                $original_img = imagecreatefromjpeg($original_dir);
                $alpha = imagecolorallocatealpha($original_img, 0, 0, 0, 63);
                //crea los registros de cara corte
                $arr_to_insert = [];
                foreach ($cortes as $corte) {
                    $id_multimedia = DB::table("csp_articulo_multimedia")->insertGetId([
                        "id_articulo" => $id_articulo,
                        "id_medio" => $mid,
                        "id_empresa" => $user->id_empresa,
                        "des_nombre_archivo" => $pid,
                        "nu_xi" => $corte["xi"],
                        "nu_yi" => $corte["yi"],
                        "nu_xf" => $corte["xf"],
                        "nu_yf" => $corte["yf"],
                        "tp_archivo" => 17
                    ]);
                    //crea el clip
                    $clip = imagecreatetruecolor($corte["sx"], $corte["sy"]);
                    imagecopyresampled($clip, $original_img, 0, 0, $corte["xi"], $corte["yi"], $corte["sx"], $corte["sy"], $corte["sx"], $corte["sy"]);
                    imagejpeg($clip, $img_dir . DIRECTORY_SEPARATOR . $id_multimedia . ".jpg");
                }
                return Response::json(["success" => true]);
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
    
    public function sv_screenshot() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($ttl,$mid,$url,$txt,$stx,$img)) {
                $id_articulo = DB::table("csp_articulo")->insertGetId([
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre" => $ttl,
                    "cod_usuario_registra" => $user->cod_usuario
                ]);
                $hoy = explode("-", date("Y-m-d"));
                $files_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "web", $hoy[0], $hoy[1], $hoy[2], $mid]);
                @mkdir($files_path, "777", true);
                $binary = base64_decode(str_replace("data:image/png;base64,", "", $img));
                $id_multimedia = DB::table("csp_articulo_multimedia")->insertGetId([
                    "id_articulo" => $id_articulo,
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre_archivo" => "-",
                    "tp_archivo" => 19
                ]);
                $img_name = $files_path . DIRECTORY_SEPARATOR . $id_multimedia . ".png";
                $xml_name = $files_path . DIRECTORY_SEPARATOR . $id_articulo . ".xml";
                file_put_contents($img_name, $binary);
                $kws = explode(",", $kws);
                $xml_content = view("components.xml_imgweb")->with(["url" => $url, "texto" => $txt, "txcorto" => $stx, "keywords" => $kws]);
                file_put_contents($xml_name, $xml_content);
                DB::table("csp_articulo_multimedia")->where("id_archivo", $id_multimedia)->update(["des_nombre_archivo" => $id_multimedia . ".png"]);
                return Response::json([
                    "success" => true,
                    "message" => "Artículo almacenado correctamente"
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

    public function sv_screenshot_rs() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($img,$mid,$aut,$ttl,$txt,$kws)) {
                $id_articulo = DB::table("csp_articulo")->insertGetId([
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre" => $ttl,
                    "des_seccion" => $aut,
                    "cod_usuario_registra" => $user->cod_usuario
                ]);
                $hoy = explode("-", date("Y-m-d"));
                $files_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "redes", $hoy[0], $hoy[1], $hoy[2], $mid]);
                @mkdir($files_path, "777", true);
                $binary = base64_decode(str_replace("data:image/png;base64,", "", $img));
                $id_multimedia = DB::table("csp_articulo_multimedia")->insertGetId([
                    "id_articulo" => $id_articulo,
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre_archivo" => "-",
                    "tp_archivo" => 18
                ]);
                $img_name = $files_path . DIRECTORY_SEPARATOR . $id_multimedia . ".png";
                $xml_name = $files_path . DIRECTORY_SEPARATOR . $id_articulo . ".xml";
                file_put_contents($img_name, $binary);
                $kws = explode(",", $kws);
                $xml_content = view("components.xml_imgredes")->with(["texto" => $txt, "keywords" => $kws]);
                file_put_contents($xml_name, $xml_content);
                DB::table("csp_articulo_multimedia")->where("id_archivo", $id_multimedia)->update(["des_nombre_archivo" => $id_multimedia . ".png"]);
                return Response::json([
                    "success" => true,
                    "message" => "Artículo almacenado correctamente"
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

    public function sv_audio() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($mid,$ttl,$prg,$kws,$hra,$mnc)) {
                $id_articulo = DB::table("csp_articulo")->insertGetId([
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre" => $ttl,
                    "des_seccion" => $prg,
                    "cod_usuario_registra" => $user->cod_usuario
                ]);
                $hoy = explode("-", date("Y-m-d"));
                $files_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "radio", $hoy[0], $hoy[1], $hoy[2], $mid]);
                @mkdir($files_path, "777", true);
                foreach($_FILES as $file) {
                    rename($file["tmp_name"], $files_path . DIRECTORY_SEPARATOR . $file["name"]);
                    $id_multimedia = DB::table("csp_articulo_multimedia")->insertGetId([
                        "id_articulo" => $id_articulo,
                        "id_medio" => $mid,
                        "id_empresa" => $user->id_empresa,
                        "des_inicio" => $mnc,
                        "des_nombre_archivo" => $file["name"],
                        "tp_archivo" => 20
                    ]);
                    //crea xml con detalles
                    $kws = explode(",", $kws);
                    $xml_name = $files_path . DIRECTORY_SEPARATOR . $id_multimedia . ".xml";
                    $xml_content = view("components.xml_audio")->with(["mencion" => $mnc, "keywords" => $kws]);
                    file_put_contents($xml_name, $xml_content);
                }
                return Response::json([
                    "success" => true,
                    "message" => "Archivo de audio registrado correctamente!"
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

    public function sv_video() {
        if(Request::ajax()) {
            $user = Auth::user();
            extract(Request::input());
            if(isset($mid,$ttl,$prg,$kws,$hra,$mnc)) {
                $id_articulo = DB::table("csp_articulo")->insertGetId([
                    "id_medio" => $mid,
                    "id_empresa" => $user->id_empresa,
                    "des_nombre" => $ttl,
                    "des_seccion" => $prg,
                    "cod_usuario_registra" => $user->cod_usuario
                ]);
                $hoy = explode("-", date("Y-m-d"));
                $files_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "tv", $hoy[0], $hoy[1], $hoy[2], $mid]);
                @mkdir($files_path, "777", true);
                foreach($_FILES as $file) {
                    rename($file["tmp_name"], $files_path . DIRECTORY_SEPARATOR . $file["name"]);
                    $id_multimedia = DB::table("csp_articulo_multimedia")->insertGetId([
                        "id_articulo" => $id_articulo,
                        "id_medio" => $mid,
                        "id_empresa" => $user->id_empresa,
                        "des_inicio" => $mnc,
                        "des_nombre_archivo" => $file["name"],
                        "tp_archivo" => 21
                    ]);
                    //crea xml con detalles
                    $kws = explode(",", $kws);
                    $xml_name = $files_path . DIRECTORY_SEPARATOR . $id_multimedia . ".xml";
                    $xml_content = view("components.xml_audio")->with(["mencion" => $mnc, "keywords" => $kws]);
                    file_put_contents($xml_name, $xml_content);
                }
                return Response::json([
                    "success" => true,
                    "message" => "Clip de video registrado correctamente!"
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