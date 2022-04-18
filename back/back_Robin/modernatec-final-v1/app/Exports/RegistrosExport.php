<?php

namespace App\Exports;

use App\Models\registro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\DB;

class RegistrosExport implements FromCollection
{
    use Exportable;

    public function __construct($fecha_1, $fecha_2)
    {
        $this->year1 = $fecha_1;/**pasamos las variables desde el controlador y se la asignamos a las variables del metodo constructor  */
        $this->year2 = $fecha_2;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
         return DB::table('registros')/**consulta eloquent para filtrar los datos  */
        ->whereBetween('fecha', [$this->year1, $this->year2])
        ->get();
       // return registro::query()->whereBetween('fecha', [$this->year1, $this->year2]);/**le damos las fechas las cuales son el filtrado para generar el reporte  */
    }
}
