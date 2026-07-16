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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama kategori (misal: Makanan, Gaji)
            $table->enum('type', ['income', 'expense']); // Jenis kategori
            $table->decimal('budget_limit', 15, 2)->nullable(); // Batas anggaran (untuk expense)
            $table->timestamps();
        });
    }
};
