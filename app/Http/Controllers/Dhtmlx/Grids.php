<?php

namespace App\Http\Controllers\Dhtmlx;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Grids extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function ls_medios() {
        $usuario = Auth::user();
        $data = DB::table("csp_medio as md")
            ->join("ma_estado_atributo as st", "md.st_medio", "=", "st.id_estado_atributo")
            ->join("ma_estado_atributo as tp", "md.tp_medio", "=", "tp.id_estado_atributo")
            ->where("md.id_empresa", $usuario->id_empresa)
            ->select("md.id_medio as medio","md.des_nombre as nombre","st.des_estado_atributo as estado","tp.des_estado_atributo as tipo","md.id_empresa as cont","md.id_empresa as pages")
        ->get();
        $hoy = explode("-", date("Y-m-d"));
        $base_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $usuario->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], ""]);
        foreach ($data as $index => $row) {
            $folder_path = $base_path . $row->medio;
            if(file_exists($folder_path)) {
                $files = scandir($folder_path);
                $number_of_pages = count($files);
                if($number_of_pages == 2) {
                    $data[$index]->cont = 0;
                    $data[$index]->pages = "(Sin imágenesss)";
                }
                else {
                    $names = [];
                    foreach ($files as $file) {
                        if(strcmp($file, ".") != 0 && strcmp($file, "..") != 0) {
                            $name = explode(".", $file);
                            array_push($names, $name[0]);
                        }
                    }
                    $data[$index]->cont = $number_of_pages - 2;
                    $data[$index]->pages = implode(", ", $names);
                }
            }
            else {
                $data[$index]->cont = 0;
                $data[$index]->pages = "(Sin imágenes)";
            }
        }
        $xml = view("dhtmlx.grid_common")->with(["data" => $data]);
        return Response::make($xml, "200")->header("Content-Type", "text/xml");
    }

    public function ls_boletines_dia() {
        $usuario = Auth::user();
        $boletines = DB::table("csp_boletin_cabecera as cab")
            ->join("csp_boletin_catalogo as cat", function($join) {
                $join->on("cab.id_boletin", "=", "cat.id_boletin")
                    ->on("cab.cod_cliente", "=", "cat.cod_cliente")
                    ->on("cab.id_empresa", "=", "cat.id_empresa");
            })
            ->join("ma_estado_atributo as sta", "cab.st_envio_boletin", "=", "sta.id_estado_atributo")
            ->join("ma_entidad as cli", "cli.cod_entidad", "=", "cat.cod_cliente")
            ->join("ma_estado_atributo as tpo", "cat.tp_boletin", "=", "tpo.id_estado_atributo")
            ->leftJoin("csp_usuario as usr", "cab.cod_usuario_genera", "=", "usr.cod_usuario")
            ->whereDate("cab.fe_programacion", date("Y-m-d"))
            ->select("cab.id_boletin as id", "cat.des_nombre as nom", "tpo.des_estado_atributo as tip", "cli.des_razon_social as cln", "sta.des_estado_atributo as est", "usr.des_alias as usn")
        ->get();
        $xml = view("dhtmlx.grid_boletines_dia")->with(["data" => $boletines]);
        return Response::make($xml, "200")->header("Content-Type", "text/xml");
    }

    public function ls_articulos_boletin_bloque($id_boletin) {
        $usuario = Auth::user();
        $hoy = explode("-", date("Y-m-d"));
        /*$boletin_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $usuario->id_empresa, "estructuras", $hoy[0], $hoy[1], $hoy[2], $id_boletin . ".xml"]);
        if(file_exists($boletin_path)) {
            //
        }
        else {*/
            $boletin = DB::table("csp_boletin_catalogo")
                ->where("id_empresa", $usuario->id_empresa)
                ->where("id_boletin", $id_boletin);
            if($boletin->count() > 0) {
                $boletin = $boletin->select("id_boletin as bid", "cod_cliente as cli", "id_empresa as emp", "tp_boletin as tpo")->first();
                $articulos = DB::table("csp_articulo as art")
                    ->join("csp_articulo_multimedia as mlt", function($join) {
                        $join->on("art.id_articulo", "=", "mlt.id_articulo")
                            ->on("art.id_medio", "=", "mlt.id_medio")
                            ->on("art.id_empresa", "=", "mlt.id_empresa");
                    })
                    ->join("csp_medio as med", function($join2) {
                        $join2->on("art.id_empresa", "=", "med.id_empresa")
                            ->on("art.id_medio", "=", "med.id_medio");
                    })
                    ->whereDate("art.created_at", date("Y-m-d"))
                    ->where("mlt.tp_archivo", $boletin->tpo);
                switch($boletin->tpo) {
                    case 17:
                        $articulos = $articulos
                            ->select("mlt.id_articulo as articulo", "art.des_nombre as titulo", "med.des_nombre as medio", DB::raw("'' as texto"), DB::raw("GROUP_CONCAT(distinct replace(mlt.des_nombre_archivo,'.jpg','') separator ', ') as paginas"), DB::raw("'' as adjuntos"), DB::raw("GROUP_CONCAT(distinct mlt.id_archivo separator ',') as archivos"))
                            ->groupBy("articulo", "titulo", "medio", "texto")
                        ->get();
                        $base_path = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $usuario->id_empresa, "diarios", $hoy[0], $hoy[1], $hoy[2], "cortes", ""]);
                        foreach($articulos as $idx => $articulo) {
                            $tx = "";
                            $arrArchivos = explode(",", $articulo->archivos);
                            sort($arrArchivos);
                            foreach ($arrArchivos as $key => $value) {
                                $curr_filename = $base_path . $value . ".txt";
                                $sub_tx = file_exists($curr_filename) ? file_get_contents($curr_filename) : "";
                                $tx .= ($key > 0 ? " " : "") . $sub_tx;
                            }
                            $articulos[$idx]->texto = $tx;
                            unset($articulos[$idx]->archivos);
                        }
                        break;
                    default: break;
                }
            }
            else $articulos = [];
            $xml = view("dhtmlx.grid_articulos")->with(["data" => $articulos]);
            return Response::make($xml, "200")->header("Content-Type", "text/xml");
        /*}*/
    }

}