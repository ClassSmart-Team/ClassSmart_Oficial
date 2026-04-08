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
        Schema::table('audits', function (Blueprint $table) {
            if (Schema::hasColumn('audits', 'metadata')) {
                $table->dropColumn('metadata');
            }

            if (Schema::hasColumn('audits', 'ip_address')) {
                $table->dropColumn('ip_address');
            }

            if (Schema::hasColumn('audits', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits', 'metadata')) {
                $table->json('metadata')->nullable()->after('description');
            }

            if (!Schema::hasColumn('audits', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('metadata');
            }

            if (!Schema::hasColumn('audits', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }
};
