<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        // atributos de un contrato de un usuario
        'fecha_inicio',
        'fecha_fin',
        'id_contrato',
    ];
}
