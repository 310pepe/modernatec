<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class citas extends Model
{
    use HasFactory;
    //atributos de la tabla citas
    protected $fillable = [
        // atributos de un usuario
        'codigo_cita',
        'fecha',
        'hora',
        'correo_solicitante',
        'correo_invitado',
        'area',
        'numero_identificacion',// llave foranea
    ];


}
