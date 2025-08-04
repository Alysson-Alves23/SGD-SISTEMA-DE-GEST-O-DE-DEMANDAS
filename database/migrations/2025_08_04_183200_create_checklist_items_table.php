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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demanda_id');
            $table->string('descricao', 500);
            $table->boolean('concluido')->default(false);
            $table->integer('ordem')->default(0);
            $table->timestamp('concluido_em')->nullable();
            $table->unsignedBigInteger('concluido_por')->nullable();
            $table->timestamps();

            $table->foreign('demanda_id')->references('numero_demanda')->on('demandas')->onDelete('cascade');
            $table->foreign('concluido_por')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['demanda_id', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};