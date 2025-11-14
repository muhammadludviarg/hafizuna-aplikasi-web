<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id_admin');
            $table->unsignedInteger('id_akun');

            $table->foreign('id_akun')->references('id_akun')->on('akun')
                  ->onDelete('cascade'); // Hapus admin jika akun dihapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
