<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $table = "csp_usuario";
    protected $primaryKey = "cod_usuario";
    protected $fillable = ["cod_usuario","id_empresa","des_alias","des_email","st_usuario","created_at","updated_at",];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    //protected $hidden = ["password", "remember_token"];
}
