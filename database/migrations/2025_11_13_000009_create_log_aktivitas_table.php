<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_akun');
            $table->dateTime('timestamp');
            $table->string('aktivitas', 50);

            $table->primary(['id_log', 'id_akun']); // Primary key ganda
            $table->foreign('id_akun')->references('id_akun')->on('akun')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};