<?php

namespace App\Http\Controllers\Multimedia;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Video extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function get_video($fid, $aid, $mid) {
        $user = Auth::user();
        $video = DB::table("csp_articulo_multimedia")
            ->where("id_archivo", $fid)
            ->where("id_articulo", $aid)
            ->where("id_medio", $mid)
            ->where("id_empresa", $user->id_empresa)
            ->select(DB::raw("date_format(created_at, '%Y-%m-%d') as fecha"), "des_nombre_archivo as archivo");
        if($video->count() > 0) {
            $video = $video->first();
            $filename = $video->archivo;
            $fecha = explode("-", $video->fecha);
            $file = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "tv", $fecha[0], $fecha[1], $fecha[2], $mid, $filename]);
            if(file_exists($file)) {
                $mime = mime_content_type($file);
                header("Content-type: " . $mime);
                header("Content-Length: " . filesize($file));
                header("Expires: -1");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                readfile($file);
            }
            else //header("HTTP/1.0 404 Not Found");
            return "archivo no existe";
        }
        else //header("HTTP/1.0 404 Not Found");
        return "registro no existe";
    }

}