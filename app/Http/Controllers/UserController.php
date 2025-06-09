<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::orderBy('nome')->get(['id', 'nome']);
    }

    public function store(Request $request)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem criar usuários.'], 403);
        }

        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'senha' => ['required', 'string', 'min:8'],
            'grupo_id' => ['required', 'integer', 'exists:grupos,id'],
            'perfil' => ['required', 'string', 'in:administrador,gestor,usuário'],
        ]);

        $user = User::create([
            'nome' => $validatedData['nome'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['senha']),
            'grupo_id' => $validatedData['grupo_id'],
            'perfil' => $validatedData['perfil'],
        ]);

        return response()->json($user, 201);
    }

    public function show(Request $request, User $user)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Não autorizado para visualizar este usuário.'], 403);
        }
        
        $user->load('grupo:id,nome');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem editar usuários.'], 403);
        }

        $validatedData = $request->validate([
            'nome' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'grupo_id' => ['integer', 'exists:grupos,id'],
            'perfil' => ['string', 'in:administrador,gestor,usuário'],
        ]);

        if ($request->filled('senha')) {
            $request->validate(['senha' => ['string', 'min:8']]);
            $validatedData['password'] = Hash::make($request->senha);
        }

        $user->update($validatedData);

        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->perfil !== 'administrador') {
            return response()->json(['error' => 'Apenas administradores podem deletar usuários.'], 403);
        }

        if ($request->user()->id === $user->id) {
            return response()->json(['error' => 'Você não pode deletar a si mesmo.'], 403);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
