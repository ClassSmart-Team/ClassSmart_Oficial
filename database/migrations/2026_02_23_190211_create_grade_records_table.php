<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('grade', 5, 2);

            // Un alumno solo puede tener UNA calificación final por unidad/grupo
            $table->unique(['student_id', 'group_id', 'unit_id'], 'grade_records_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_records');
    }
};


