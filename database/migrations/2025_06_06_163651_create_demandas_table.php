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
        Schema::create('demandas', function (Blueprint $table) {
            $table->id('numero_demanda');
            $table->enum('tipo', ['Demanda Padrão','Suporte Técnico','Demanda Comercial','Demanda de Estoque','Demanda de Manutenção','Demanda Produção','Demanda de Produção','Demanda de Serviços Gerais']);
            $table->string('empresa', 100);
            $table->enum('natureza', ['SERVIÇOS GERAIS','PRODUÇÃO','SUPORTE E MANUTENÇÃO','PRODUÇÃO P/ ESTOQUE','INTERNA']);
            $table->string('numero_pedido_ou_nf', 50)->nullable();
            $table->timestamp('data_recebimento')->nullable(); // Ajustado para timestamp
            $table->date('prazo_execucao');
            $table->string('cliente', 100);
            $table->text('descricao_itens')->nullable();
            $table->date('data_planejamento')->nullable();
            $table->date('inicio_execucao')->nullable();
            $table->date('finalizacao_execucao')->nullable();
            $table->enum('status_planejamento', ['Finalizada','Aberta','Fechada','Em Andamento','Pendente']);
            $table->enum('status_execucao', ['Finalizada','Aberta','Fechada','Em Andamento','Pendente']);
            $table->text('descricao_pendencia')->nullable();
            $table->unsignedBigInteger('executor_id');
            $table->string('tempo_execucao', 50)->nullable();
            $table->date('previsao_inicio')->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('solicitante_id');
            
            $table->timestamp('criado_em')->nullable()->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();

            $table->foreign('executor_id')->references('id')->on('users');
            $table->foreign('solicitante_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
