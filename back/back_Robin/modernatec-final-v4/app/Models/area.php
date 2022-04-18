<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',            //nombre del area la cual se le asigana a un usuario
    ];
}

