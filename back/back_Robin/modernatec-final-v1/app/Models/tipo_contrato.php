<?php
/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipo_contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_contrato',
    ];
}
