<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandaAtualizacao extends Model
{
    use HasFactory;
    
    protected $table = 'atualizacoes_demanda';

    const CREATED_AT = 'data';
    const UPDATED_AT = null;
    
    protected $guarded = [];

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class, 'demanda_id', 'numero_demanda');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}