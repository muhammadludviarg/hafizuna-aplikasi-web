<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->increments('id_ortu');
            $table->string('no_hp', 20)->nullable();
            $table->unsignedInteger('id_akun')->nullable();

            $table->foreign('id_akun')->references('id_akun')->on('akun')
                ->onDelete('set null'); // Jika akun dihapus, id_akun jadi null
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};