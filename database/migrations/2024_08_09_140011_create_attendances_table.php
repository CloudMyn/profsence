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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->date('date');
            $table->time('time');

            $table->string('photo', 255);
            $table->string('note')->nullable();

            $table->enum('type', ['check_in', 'check_out']);

            $table->string('latitude');
            $table->string('longitude');

            $table->boolean('check_violation')->default(false);
            $table->string('violation_note')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
