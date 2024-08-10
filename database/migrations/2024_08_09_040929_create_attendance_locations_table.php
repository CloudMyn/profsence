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
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();

            $table->string('photo')->nullable();

            $table->string('name');
            $table->string('address', 199);

            $table->enum('color', ['red', 'green', 'yellow', 'orange', 'blue', 'black', 'grey', 'violet'])->default('red');

            $table->integer('allowance')->default(15)->comment('allowance in minutes');
            $table->integer('radius')->default(20)->comment('location radius in meter');

            $table->string('latitude');
            $table->string('longitude');

            $table->time('time_in');
            $table->time('time_out');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_locations');
    }
};
