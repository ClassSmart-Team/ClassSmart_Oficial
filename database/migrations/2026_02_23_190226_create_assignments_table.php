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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', [
                'Activa',    // recién asignada, aceptando entregas
                'Cerrada',   // venció, ya no acepta entregas
                'Cancelada', // el maestro la canceló
            ])->default('Activa');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};


