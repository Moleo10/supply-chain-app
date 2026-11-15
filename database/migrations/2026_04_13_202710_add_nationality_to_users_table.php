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
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل الجنسية بعد حقل الدور (role) ويكون قابلاً لأن يكون فارغاً
            $table->string('nationality')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الحقل في حال تراجعنا عن العملية (Rollback)
            $table->dropColumn('nationality');
        });
    }
};