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
        Schema::create('cantidad_billetes', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('billetes10')->unsigned()->default(0);
            $table->smallInteger('billetes20')->unsigned()->default(0);
            $table->smallInteger('billetes50')->unsigned()->default(0);
            $table->smallInteger('billetes100')->unsigned()->default(0);
            $table->smallInteger('billetes200')->unsigned()->default(0);
            $table->smallInteger('billetes500')->unsigned()->default(0);
            $table->smallInteger('billetes1000')->unsigned()->default(0);
            $table->smallInteger('monedas1')->unsigned()->default(0);
            $table->smallInteger('monedas5')->unsigned()->default(0);
            $table->smallInteger('dolares1')->unsigned()->default(0);
            $table->smallInteger('dolares5')->unsigned()->default(0);
            $table->smallInteger('dolares10')->unsigned()->default(0);
            $table->smallInteger('dolares20')->unsigned()->default(0);
            $table->smallInteger('dolares50')->unsigned()->default(0);
            $table->smallInteger('dolares100')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantidad_billetes');
    }
};
