<?php

/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class registro extends Model
{
    use HasFactory;

    protected $fillable = [

        // atributos de un registro para ingreso y salida
        'id',
        'fecha',
        'hora_ingreso',
        'hora_salida',
        'numero_identificacion', // llave foranea

    ];
}
