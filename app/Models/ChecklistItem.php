<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'demanda_id',
        'descricao',
        'concluido',
        'ordem',
        'concluido_em',
        'concluido_por'
    ];

    protected $casts = [
        'concluido' => 'boolean',
        'concluido_em' => 'datetime',
    ];

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class, 'demanda_id', 'numero_demanda');
    }

    public function concluidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concluido_por');
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem', 'asc');
    }

    public function scopeConcluidos($query)
    {
        return $query->where('concluido', true);
    }

    public function scopePendentes($query)
    {
        return $query->where('concluido', false);
    }
}