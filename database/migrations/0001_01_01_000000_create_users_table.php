<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti 'users' menjadi 'akun'
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun'); // Sesuai .sql
            $table->string('email', 255)->unique(); // Sesuai .sql
            $table->text('sandi_hash'); // Sesuai .sql
            $table->string('nama_lengkap', 255); // Sesuai .sql
            $table->boolean('status')->default(true); // Sesuai .sql
            $table->timestamp('email_verified_at')->nullable(); // Bawaan Breeze, biarkan saja
            $table->rememberToken(); // Bawaan Breeze, biarkan saja
            $table->datetime('dibuat_pada')->default(DB::raw('CURRENT_TIMESTAMP')); // Sesuai .sql
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
