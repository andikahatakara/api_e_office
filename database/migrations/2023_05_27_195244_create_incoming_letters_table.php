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
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('input_by');
            $table->unsignedBigInteger('to');
            $table->string('from');
            $table->string('number');
            $table->date('date');
            $table->string('characteristic');
            $table->string('file');
            $table->string('about');
            $table->timestamps();

            $table->foreign('input_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('to')
                ->references('user_id')
                ->on('employees')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('incoming_letters', function(Blueprint $table) {
            $table->dropForeign(['input_by']);
            $table->dropForeign(['to']);
        });
        Schema::dropIfExists('incoming_letters');
    }
};
