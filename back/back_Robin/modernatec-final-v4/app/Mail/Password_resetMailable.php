<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Password_resetMailable extends Mailable
{
    public $subject = "Olvide mi contraseña";

    private $codigo;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        return $this->view('emails.Password_reset') //devolvemos una vista y ademas le pasamos la variable con el codigo que va a ser utilizado
        ->with([
            'code' => $this->codigo,  // codigo que se va a enviar
        ]);
    }
}
