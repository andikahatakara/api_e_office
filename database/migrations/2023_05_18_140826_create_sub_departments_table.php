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
        Schema::create('sub_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('slug');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_departments', function(Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::dropIfExists('sub_departments');
    }
};
