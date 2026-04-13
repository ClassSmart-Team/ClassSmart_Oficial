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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('email_notification')->default(true);
            $table->boolean('push_notification')->default(true);

            // Subopciones Email
            $table->boolean('email_new_assignments')->default(true);
            $table->boolean('email_submissions')->default(true);
            $table->boolean('email_grades')->default(true);
            $table->boolean('email_feedback')->default(true);
            $table->boolean('email_announcements')->default(true);
            $table->boolean('email_grade_records')->default(true);

            // Subopciones Push
            $table->boolean('push_new_assignments')->default(true);
            $table->boolean('push_submissions')->default(true);
            $table->boolean('push_grades')->default(true);
            $table->boolean('push_feedback')->default(true);
            $table->boolean('push_announcements')->default(true);
            $table->boolean('push_grade_records')->default(true);

            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
