<?php

/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */



namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{

    public function __construct($numero_identificacion, $email)
    {
        $this->numero_identificacion = $numero_identificacion;/**variables resibidas desde el controlador de Registro en el metodo constructor  */
        $this->email = $email;
        $this->error = array();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {


/**Robin David Rodriguez Bautista
 * En colaboracion con Jose Bermudez
 * 17-03-2022
 * Estudiante adsi
 */


        if (in_array($row['numero_identificacion'], $this->numero_identificacion ))/**en caso de que al usuario se le repita el numero de identificacion se le mostrara la siguiente informacion  */
        {
            array_push($this->error, [
                'numero_identificacion' => $row['numero_identificacion'],
                'name' => $row['name'],
                'apellidos' => $row['apellidos'],
                'email' => $row['email'],
                'telefono' => $row['telefono'],
            ]);

        }elseif(in_array($row['email'], $this->email ))/**en caso de que el email ya este registrado se le mostrara el siguiente mensaje  */
        {

            array_push($this->error, [
                'numero_identificacion' => $row['numero_identificacion'],
                'name' => $row['name'],
                'apellidos' => $row['apellidos'],
                'email' => $row['email'],
                'telefono' => $row['telefono'],
            ]);

        }else{/**en caso de que todo la validacion este correcta se realizara la insercion de los datos  */

            return new User([
                'numero_identificacion' => $row['numero_identificacion'],
                'name' => $row['name'],
                'apellidos' => $row['apellidos'],
                'email' => $row['email'],
                'telefono' => $row['telefono'],
            ]);
        }

    }

    public function batchSize(): int
    {
        return 4000;
    }

    public function chunkSize(): int
    {
        return 4000;
    }
}
