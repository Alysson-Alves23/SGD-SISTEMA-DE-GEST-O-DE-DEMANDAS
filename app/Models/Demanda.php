<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Demanda extends Model
{
    use HasFactory;

    protected $primaryKey = 'numero_demanda';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $guarded = [];

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function atualizacoes(): HasMany
    {
        return $this->hasMany(DemandaAtualizacao::class, 'demanda_id', 'numero_demanda');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'demanda_id', 'numero_demanda')->ordenados();
    }

    public function checklistItemsConcluidos(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'demanda_id', 'numero_demanda')->concluidos()->ordenados();
    }

    public function checklistItemsPendentes(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'demanda_id', 'numero_demanda')->pendentes()->ordenados();
    }
}