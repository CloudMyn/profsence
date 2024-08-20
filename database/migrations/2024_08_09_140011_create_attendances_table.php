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
            $table->foreignId('location_id')->nullable();

            $table->date('date');
            $table->time('time');

            $table->string('photo', 255)->nullable();
            $table->string('note', 199)->nullable();

            $table->enum('type', ['check_in', 'check_out', 'sakit', 'alfa', 'cuti', 'dinas_luar']);

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->boolean('check_violation')->default(false);
            $table->string('violation_note')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('attendance_locations')->onDelete('cascade');
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
