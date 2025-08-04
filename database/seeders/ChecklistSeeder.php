<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Demanda;
use App\Models\ChecklistItem;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar algumas demandas existentes para adicionar checklist
        $demandas = Demanda::take(5)->get();
        
        foreach ($demandas as $demanda) {
            // Criar alguns itens de checklist para cada demanda
            $checklistItems = [
                'Verificar documentação necessária',
                'Analisar requisitos técnicos',
                'Preparar ambiente de desenvolvimento',
                'Implementar funcionalidades básicas',
                'Realizar testes unitários',
                'Documentar alterações',
                'Preparar para deploy'
            ];
            
            foreach ($checklistItems as $index => $descricao) {
                ChecklistItem::create([
                    'demanda_id' => $demanda->numero_demanda,
                    'descricao' => $descricao,
                    'concluido' => $index < 3, // Primeiros 3 itens como concluídos
                    'ordem' => $index + 1,
                    'concluido_em' => $index < 3 ? now()->subDays(rand(1, 7)) : null,
                    'concluido_por' => $index < 3 ? $demanda->executor_id : null,
                ]);
            }
        }
    }
}