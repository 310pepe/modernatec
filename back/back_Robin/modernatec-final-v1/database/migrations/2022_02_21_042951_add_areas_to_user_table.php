<?php

/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreasToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_area')->nullable();
            $table->unsignedBigInteger('id_contrato')->nullable()->unique();
            $table->foreign('id_area')->references('id')->on('areas')->onDelete('set null');
            $table->foreign('id_contrato')->references('id')->on('contratos')->onDelete('cascade')->onUpdate("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
