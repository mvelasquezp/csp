<?php

namespace App\Http\Controllers\Multimedia;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use Validator;

class Audio extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
    }

    public function get_audio($fid, $aid, $mid) {
        $user = Auth::user();
        $audio = DB::table("csp_articulo_multimedia")
            ->where("id_archivo", $fid)
            ->where("id_articulo", $aid)
            ->where("id_medio", $mid)
            ->where("id_empresa", $user->id_empresa)
            ->select(DB::raw("date_format(created_at, '%Y-%m-%d') as fecha"), "des_nombre_archivo as archivo");
        if($audio->count() > 0) {
            $audio = $audio->first();
            $filename = $audio->archivo;
            $fecha = explode("-", $audio->fecha);
            $file = implode(DIRECTORY_SEPARATOR, [env("APP_DISK"), $user->id_empresa, "radio", $fecha[0], $fecha[1], $fecha[2], $mid, $filename]);
            $extension = "mp3";
            $mime_type = "audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3";
            if(file_exists($file)) {
                header('Content-type: {$mime_type}');
                header('Content-length: ' . filesize($file));
                header('Content-Disposition: filename="' . $filename);
                header('X-Pad: avoid browser bug');
                header('Cache-Control: no-cache');
                readfile($file);
            }
            else header("HTTP/1.0 404 Not Found");
        }
        else header("HTTP/1.0 404 Not Found");
    }

}