<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'nome',
        'email',
        'senha_hash',
        'perfil',
        'grupo_id',
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'nome' => $this->nome,
            'perfil' => $this->perfil,
            'grupo_id' => $this->grupo_id,
        ];
    }

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function getAuthPasswordName()
    {
        return 'senha_hash';
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function demandasCriadas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'solicitante_id');
    }

    public function demandasAtribuidas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'executor_id');
    }
}