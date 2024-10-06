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
        Schema::create('recuperacion_dia', function (Blueprint $table) {
            $table->id();
            $table->decimal('montoCordobas', 9, 2)->unsigned()->default(0);
            $table->decimal('montoRecolectadoDia', 9, 2)->unsigned()->default(0);
            $table->string('descripcion')->nullable();
            $table->decimal('montoDolares', 9, 2)->unsigned()->default(0);
            $table->decimal('represtamo', 9, 2)->unsigned()->default(0);
            $table->decimal('gastos', 9, 2)->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recuperacion_dia');
    }
};
