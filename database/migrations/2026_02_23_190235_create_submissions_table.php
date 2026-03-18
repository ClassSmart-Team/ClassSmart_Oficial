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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('submission_date');
            $table->enum('status', [
                'Entregada',        // entregó a tiempo
                'Entregada tarde',  // entregó pero después de la fecha límite
                'Calificada',       // el maestro ya le puso nota
            ])->default('Entregada');
            $table->decimal('grade', 4, 2)->nullable(); // null = aún no calificada
            $table->text('feedback')->nullable();        // comentarios del maestro
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
