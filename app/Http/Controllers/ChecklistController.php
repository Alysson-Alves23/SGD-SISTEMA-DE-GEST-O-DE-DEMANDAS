<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    /**
     * Adicionar um novo item ao checklist
     */
    public function store(Request $request, Demanda $demanda)
    {
        $validatedData = $request->validate([
            'descricao' => 'required|string|max:500',
        ]);

        // Determinar a ordem do novo item
        $maxOrdem = $demanda->checklistItems()->max('ordem') ?? 0;
        
        $item = $demanda->checklistItems()->create([
            'descricao' => $validatedData['descricao'],
            'ordem' => $maxOrdem + 1,
        ]);

        return response()->json([
            'message' => 'Item adicionado com sucesso!',
            'item' => $item
        ], 201);
    }

    /**
     * Atualizar um item do checklist
     */
    public function update(Request $request, Demanda $demanda, ChecklistItem $item)
    {
        $validatedData = $request->validate([
            'descricao' => 'sometimes|required|string|max:500',
            'concluido' => 'sometimes|boolean',
        ]);

        if (isset($validatedData['concluido'])) {
            if ($validatedData['concluido']) {
                $validatedData['concluido_em'] = now();
                $validatedData['concluido_por'] = Auth::id();
            } else {
                $validatedData['concluido_em'] = null;
                $validatedData['concluido_por'] = null;
            }
        }

        $item->update($validatedData);

        return response()->json([
            'message' => 'Item atualizado com sucesso!',
            'item' => $item->fresh()
        ]);
    }

    /**
     * Remover um item do checklist
     */
    public function destroy(Demanda $demanda, ChecklistItem $item)
    {
        $item->delete();

        // Reordenar os itens restantes
        $demanda->checklistItems()->ordenados()->get()->each(function ($item, $index) {
            $item->update(['ordem' => $index + 1]);
        });

        return response()->json([
            'message' => 'Item removido com sucesso!'
        ]);
    }

    /**
     * Reordenar itens do checklist
     */
    public function reorder(Request $request, Demanda $demanda)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:checklist_items,id',
            'items.*.ordem' => 'required|integer|min:1',
        ]);

        foreach ($validatedData['items'] as $itemData) {
            ChecklistItem::where('id', $itemData['id'])
                ->where('demanda_id', $demanda->numero_demanda)
                ->update(['ordem' => $itemData['ordem']]);
        }

        return response()->json([
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }

    /**
     * Marcar/desmarcar item como concluído
     */
    public function toggleConcluido(Request $request, Demanda $demanda, ChecklistItem $item)
    {
        $concluido = !$item->concluido;
        
        $item->update([
            'concluido' => $concluido,
            'concluido_em' => $concluido ? now() : null,
            'concluido_por' => $concluido ? Auth::id() : null,
        ]);

        return response()->json([
            'message' => $concluido ? 'Item marcado como concluído!' : 'Item desmarcado!',
            'item' => $item->fresh()
        ]);
    }
}