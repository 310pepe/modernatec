<?php

/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificacionMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Confirmacion de correo de contacto"; //le asignamos un titilo al correo que vamos a enviar con la propiedad $subject

    private $codigo;  //declaramos la variable codigo la cual va a tener el codigo que se va a confirmar

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code) // como parametro le pasamos el codigo al metodo constrictor para tener el codigo en este controlador
    {
        $this->codigo = $code; // le asignamos el valor del codigo a la variable privada
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.Verificacion') //devolvemos una vista y ademas le pasamos la variable con el codigo que va a ser utilizado
        ->with([
            'code' => $this->codigo,  // codigo que se va a enviar
        ]);
    }
}
