<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->increments('id_akun'); // Sesuai int(11) auto_increment
            $table->string('email', 30)->unique();
            $table->text('sandi_hash');
            $table->string('nama_lengkap', 50);
            $table->boolean('status')->default(true);
            $table->datetime('dibuat_pada')->useCurrent(); // Sesuai default current_timestamp()
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};