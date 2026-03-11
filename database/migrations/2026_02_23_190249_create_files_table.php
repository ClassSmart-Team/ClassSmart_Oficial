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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained('submissions')->onDelete('cascade');
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('context', [
                'assignment_material', // archivo que subió el maestro a la tarea
                'student_submission',  // archivo que entregó el alumno
            ])->default('assignment_material');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('type');
            $table->integer('size');
            $table->timestamps();
        });

        // Garantiza que todo archivo tenga al menos un dueño lógico
        DB::statement('
            ALTER TABLE files
            ADD CONSTRAINT chk_files_has_owner
            CHECK (submission_id IS NOT NULL OR assignment_id IS NOT NULL)
        ');
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        DB::statement('ALTER TABLE files DROP CONSTRAINT chk_files_has_owner');
        Schema::dropIfExists('files');
    }
};
