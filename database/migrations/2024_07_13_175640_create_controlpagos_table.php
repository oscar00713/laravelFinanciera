<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('controlpagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->integer('concepto')->default(1);
            $table->smallInteger('frecuencia'); //  1 = semanal, 2 = mensual
            $table->integer('plazo');
            $table->integer('cuotas'); //numero de cuotas
            $table->smallInteger('status'); //1 = nuevo, 2 = represtamo
            $table->smallInteger('diaCobro'); //1 = lunes, 2 = martes, 3 = miercoles, 4 = jueves, 5 = viernes, 6 = sabado, 7 = domingo
            $table->date('fechaContrato');
            $table->smallInteger('mes'); //1 = enero, 2 = febrero, 3 = marzo, 4 = abril, 5 = mayo, 6 = junio, 7 = julio, 8 = agosto, 9 = septiembre, 10 = octubre, 11 = noviembre, 12 = diciembre
            $table->decimal('montoPrestado', 9, 2)->unsigned();
            $table->decimal('interes', 9, 2)->unsigned();
            $table->date('primerCobro'); //fecha del primer cobro
            $table->decimal('cuota', 9, 2)->unsigned();
            $table->decimal('montoPendiente', 9, 2)->unsigned();
            $table->decimal('interes_cuota', 9, 2)->unsigned(); //este interes se envia para verificar el total de interes que se paga
            $table->decimal('totalInteres', 9, 2)->unsigned();
            $table->boolean('creditoTerminado')->default(false);
            $table->decimal('total', 9, 2)->unsigned(); //total a pagar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlpago');
    }
};
