<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Demanda;
use App\Models\DemandaAtualizacao;

class PageController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }
    
    public function showRegister()
    {
        $grupos = Grupo::all(['id', 'nome']);
        return view('cadastro', ['grupos' => $grupos]);
    }

    public function showDashboard()
    {
        return view('dashboard');
    }
    public function showCreateDemandForm()
    {
        return view('demanda-form-create');
    }
        
    public function showDemandDetail()
    {
        return view('demanda-detalhe');
    }

    public function showEditDemandForm()
    {
        return view('demanda-form-edit');
    }


}