<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('controlpago_id')->constrained('controlpagos');
            $table->integer('numAbono')->unsigned()->default(0); // calculado automaticamente en la base de datos
            $table->date('fechaProximoAbono')->nullable();
            $table->decimal('montoAbono')->unsigned()->default(0);
            $table->integer('estado')->unsigned(); // 1 = pagado, 2 = pendiente, 3 = vencido (si el cliente no paga el abono)
            $table->decimal('interesAbono', 9, 2)->unsigned()->default(0); //calculado automaticamente al ingresar los datos
            $table->decimal('total', 9, 2)->unsigned()->default(0); //calculado automaticamente al ingresar los datos

            $table->timestamps(); //created_at, updated_at parasaler la fecha que pago el abono
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
        // Eliminar los triggers si existen

    }
};
