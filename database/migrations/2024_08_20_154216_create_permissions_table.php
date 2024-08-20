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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('document_proof');
            $table->enum('type', ['cuti', 'sakit', 'dinas_luar']); // Jenis izin (sakit, cuti, izin, dll.)
            $table->text('description', 199)->nullable(); // Deskripsi atau alasan izin
            $table->date('start_date'); // Tanggal mulai izin
            $table->date('end_date')->nullable(); // Tanggal akhir izin, bisa nullable jika hanya satu hari
            $table->integer('duration');
            $table->timestamp('approved_at')->nullable(); // Tanggal persetujuan izin
            $table->string('approved_by')->nullable(); // ID user yang menyetujui izin
            $table->unsignedBigInteger('user_id'); // ID user yang mengajukan izin
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
