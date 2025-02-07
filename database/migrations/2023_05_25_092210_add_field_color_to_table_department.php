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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('color')->after('slug')->default('default');
        });
        Schema::table('sub_departments', function (Blueprint $table) {
            $table->string('color')->after('slug')->default('default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('color');
        });
        Schema::table('sub_departments', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
