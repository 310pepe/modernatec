<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Destroy_Cita_Mailable extends Mailable
{
    use Queueable, SerializesModels;
    public $subject = "Cancelacion de cita";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $codigo;
    public function __construct($code)
    {
        $this->codigo = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.Destroy_Cita') //devolvemos una vista y ademas le pasamos la variable con el codigo que va a ser utilizado
        ->with([
            'code' => $this->codigo,  // codigo que se va a enviar
        ]);
    }
}
