<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->increments('id_reset');
            $table->unsignedInteger('id_akun');
            $table->string('token', 70);
            $table->dateTime('kadaluarsa_pada');

            $table->primary(['id_reset', 'id_akun']); // Primary key ganda
            $table->foreign('id_akun')->references('id_akun')->on('akun')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};