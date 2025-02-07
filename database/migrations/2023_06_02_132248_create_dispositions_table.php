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
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_letter_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->string('note')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('from')
                ->references('user_id')
                ->on('employees')
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
        Schema::dropIfExists('dispositions');
    }
};
