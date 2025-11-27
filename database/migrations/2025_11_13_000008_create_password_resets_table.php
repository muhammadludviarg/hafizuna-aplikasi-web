<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PENTING: Untuk kompatibilitas penuh dengan Laravel Authentication,
        // kita menggunakan skema default: 'email', 'token', 'created_at'.
        // Jika Anda menggunakan Laravel 10+, nama tabel default-nya adalah
        // 'password_reset_tokens'. Kita gunakan nama yang Anda miliki
        // tetapi dengan skema kolom yang benar.

        // Kita ubah agar menggunakan konvensi Laravel agar fitur bawaan berfungsi.
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->primary(); // Harus 'email' dan dijadikan primary key
            $table->string('token');
            $table->timestamp('created_at')->nullable(); // Harus 'created_at' untuk tracking
        });

        // Buat index pada email untuk performa.
        Schema::table('password_resets', function (Blueprint $table) {
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};