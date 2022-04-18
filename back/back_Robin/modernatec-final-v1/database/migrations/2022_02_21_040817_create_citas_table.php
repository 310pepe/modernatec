<?php


/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('codigo_cita')->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->string('correo_solicitante');
            $table->string('correo_invitado');
            $table->string('area');
            $table->string('confirm');
            $table->unsignedBigInteger('numero_identificacion')->nullable();
            $table->foreign('numero_identificacion')->references('id')
            ->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citas');
    }
}
