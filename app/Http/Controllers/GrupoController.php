<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Listar todos os grupos.
     * Acessível por administradores e gestores.
     */
    public function index(Request $request)
    {
        if (!in_array($request->user()->perfil, ['administrador', 'gestor'])) {
            return response()->json(['error' => 'Não autorizado para visualizar grupos.'], 403);
        }

        return Grupo::withCount('usuarios')->orderBy('nome')->get();
    }

    /**
     * Criar um novo grupo.
     * Acessível apenas por administradores.
     */
    public function store(Request $request)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem criar grupos.'], 403);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:100|unique:grupos',
            'descricao' => 'nullable|string',
        ]);

        $grupo = Grupo::create($validatedData);

        return response()->json($grupo, 201);
    }

    /**
     * Exibir um grupo específico e seus membros.
     * Acessível por administradores e gestores.
     */
    public function show(Request $request, Grupo $grupo)
    {
        if (!in_array($request->user()->perfil, ['administrador', 'gestor'])) {
            return response()->json(['error' => 'Não autorizado para visualizar este grupo.'], 403);
        }

        $grupo->load('usuarios:id,nome,email');

        return response()->json($grupo);
    }

    /**
     * Atualizar um grupo existente.
     * Acessível apenas por administradores.
     */
    public function update(Request $request, Grupo $grupo)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem editar grupos.'], 403);
        }

        $validatedData = $request->validate([
            'nome' => 'string|max:100|unique:grupos,nome,' . $grupo->id,
            'descricao' => 'nullable|string',
        ]);

        $grupo->update($validatedData);

        return response()->json($grupo);
    }

    /**
     * Deletar um grupo.
     * Acessível apenas por administradores.
     */
    public function destroy(Request $request, Grupo $grupo)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem deletar grupos.'], 403);
        }

        if ($grupo->usuarios()->count() > 0) {
            return response()->json(['error' => 'Não é possível deletar um grupo que ainda possui usuários.'], 422);
        }
        
        $grupo->delete();

        return response()->json(null, 204);
    }
}