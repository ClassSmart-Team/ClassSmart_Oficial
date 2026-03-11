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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('name', 100);                    // Ej: "Unidad 1", "Bloque A", lo que sea
            $table->unsignedTinyInteger('order')->default(1); // Para ordenarlas correctamente
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /*
    ahi no entiendo lo de ordenarlas
    El campo order es para saber en qué orden mostrarlas en pantalla.
    Sin él, si tienes por ejemplo:
    "Bloque B"
    "Unidad 1"
    "Bloque A"
    La base de datos no sabe cuál va primero. Con order tú le dices explícitamente: esta va de 1ra, esta de 2da, esta de 3ra — y en tu app haces orderBy('order') 
    para mostrarlas siempre en el orden correcto.
    Dicho esto, si tu app siempre va a usar "Unidad 1,
    Unidad 2, Unidad 3" en ese orden y no más, el campo order no es estrictamente necesario. Lo agregué pensando en flexibilidad futura.
    
    te recomiendo dejarlo.
    Es un campo pequeño que no complica nada y te salva de dolores de cabeza en el futuro si el cliente pide cambiar la estructura de unidades. 
    Mejor tenerlo y no necesitarlo que necesitarlo y no tenerlo. 😄
     */
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
