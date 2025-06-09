<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandaController extends Controller
{
    /**
     * Listar as demandas (Painel de Demandas / Dashboard).
     * Esta função substitui sua API /api/dashboard-data.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Obtém o usuário autenticado
        $query = Demanda::with('solicitante:id,nome', 'executor:id,nome'); // Eager load para performance

        // Lógica de filtro baseada no perfil e no parâmetro 'view'
        $view = $request->query('view', 'all');

        if ($user->perfil === 'usuário' || $view === 'user') {
            $query->where(function ($q) use ($user) {
                $q->where('solicitante_id', $user->id)
                  ->orWhere('executor_id', $user->id);
            });
        } elseif ($user->perfil === 'gestor' || $view === 'group') {
            $query->whereHas('solicitante', function ($q) use ($user) {
                $q->where('grupo_id', $user->grupo_id);
            })->orWhereHas('executor', function ($q) use ($user) {
                $q->where('grupo_id', $user->grupo_id);
            });
        }
        // Se for 'administrador' e a view for 'all', não aplica filtro de usuário/grupo.

        // Filtro adicional por status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_execucao', $request->status);
        }

        $demandas = $query->orderBy('numero_demanda', 'desc')->get();

        // Monta a resposta JSON para o frontend
        return response()->json([
            'demands' => $demandas,
            'userInfo' => ['nome' => $user->nome],
            'viewFilter' => $view,
            'statusFilter' => $request->status,
            'allStatuses' => ['Aberta', 'Em Andamento', 'Pendente', 'Em Pausa', 'Concluída', 'Fechado']
        ]);
    }

    /**
     * Salvar uma nova demanda no banco de dados.
     * Esta função substitui sua API /api/demandas/criar.
     */
    public function store(Request $request)
    {
        // Validação dos dados recebidos do formulário
        $validatedData = $request->validate([
            'tipo' => 'required|string',
            'empresa' => 'nullable|string|max:100',
            'natureza' => 'nullable|string',
            'cliente' => 'nullable|string|max:100',
            'numero_pedido_ou_nf' => 'nullable|string|max:50',
            'prazo_execucao' => 'nullable|date',
            'executor_id' => 'nullable|exists:users,id',
            'descricao_itens' => 'required|string',
            'observacoes' => 'nullable|string',
        ]);

        // Adiciona o ID do usuário logado como solicitante
        $validatedData['solicitante_id'] = $request->user()->id;
        // Adiciona um status padrão
        $validatedData['status_execucao'] = 'Aberta';

        $demanda = Demanda::create($validatedData);

        return response()->json([
            'message' => 'Demanda cadastrada com sucesso!',
            'demanda' => $demanda
        ], 201); // 201 Created
    }

    /**
     * Exibir os detalhes de uma demanda específica.
     * Esta função substitui sua API /api/demanda/{id}.
     */
    public function show(Demanda $demanda)
    {
        // Graças ao Route-Model Binding, o Laravel já encontrou a demanda pelo ID na URL.
        // Apenas carregamos os relacionamentos para incluir na resposta.
        $demanda->load('solicitante:id,nome', 'executor:id,nome', 'atualizacoes.usuario:id,nome');

        return response()->json([
            'demanda' => $demanda,
            'historico' => $demanda->atualizacoes, // O relacionamento já foi carregado
            'userInfo' => ['nome' => Auth::user()->nome]
        ]);
    }

    /**
     * Atualizar uma demanda existente no banco de dados.
     * Esta função substitui sua API /api/demandas/atualizar/{id}.
     */
    public function update(Request $request, Demanda $demanda)
    {
        // Validação expandida para todos os campos do formulário de edição
        $validatedData = $request->validate([
            'tipo' => 'required|string|max:100',
            'empresa' => 'nullable|string|max:100',
            'natureza' => 'nullable|string|max:100',
            'cliente' => 'nullable|string|max:100',
            'numero_pedido_ou_nf' => 'nullable|string|max:50',
            'prazo_execucao' => 'nullable|date',
            'data_planejamento' => 'nullable|date',
            'previsao_inicio' => 'nullable|date',
            'inicio_execucao' => 'nullable|date',
            'finalizacao_execucao' => 'nullable|date',
            'status_planejamento' => 'nullable|string|max:50',
            'status_execucao' => 'required|string|max:50',
            'tempo_execucao' => 'nullable|string|max:50',
            'descricao_itens' => 'required|string',
            'descricao_pendencia' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'mudanca_descricao' => 'required|string|max:500', // Descrição da alteração vinda do modal
        ]);

        // Remove a descrição da mudança do array antes de atualizar a demanda
        $mudancaDescricao = $validatedData['mudanca_descricao'];
        unset($validatedData['mudanca_descricao']);

        // Atualiza a demanda com os dados validados
        $demanda->update($validatedData);

        // Adiciona um registro no histórico de atualizações
        $demanda->atualizacoes()->create([
            'usuario_id' => $request->user()->id,
            'descricao' => $mudancaDescricao, // Usa a descrição fornecida no modal
            'status' => $request->input('status_execucao')
        ]);

        return response()->json(['message' => 'Demanda atualizada com sucesso!', 'demanda' => $demanda->fresh()]);
    }

    /**
     * Remover uma demanda do banco de dados.
     */
    public function destroy(Demanda $demanda)
    {
        // Adicionar lógica de autorização aqui (ex: só admin pode deletar)
        // if (Auth::user()->perfil !== 'administrador') {
        //     return response()->json(['error' => 'Não autorizado'], 403);
        // }

        $demanda->delete();

        return response()->json(null, 204); // 204 No Content
    }
}