<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'nome' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'senha' => ['required', 'string', 'min:8'],
        'grupo_id' => ['required', 'integer', 'exists:grupos,id'],
        'perfil' => ['required', 'string', 'in:administrador,gestor,usuário'],
    ]);

    $user = User::create([
        'nome' => $request->nome,
        'email' => $request->email,
        'senha_hash' => Hash::make($request->senha),
        'grupo_id' => $request->grupo_id,
        'perfil' => $request->perfil,
    ]);

    $token = auth('api')->login($user);

    return response()->json([
        'message' => 'Usuário criado com sucesso!',
        'user' => $user,
        'access_token' => $token
    ], 201);
}
    
}