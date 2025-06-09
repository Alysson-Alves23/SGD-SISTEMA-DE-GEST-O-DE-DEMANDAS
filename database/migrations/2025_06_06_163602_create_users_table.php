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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('email')->unique();
        $table->string('senha_hash'); 
        $table->string('perfil'); 
        $table->unsignedBigInteger('grupo_id');
        $table->rememberToken();
        $table->timestamp('criado_em')->nullable()->useCurrent();
        $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();

         $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
