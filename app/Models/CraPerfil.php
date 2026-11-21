<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Dados institucionais do CRA (registro único)
class CraPerfil extends Model
{
    // Registro do CRA na tabela unidade_operacional (fonte do responsável e do substituto)
    public const UNIDADE_OPERACIONAL_ID = 819200;

    protected $table = 'cra_perfil';

    protected $primaryKey = 'cra_perfil_id';

    protected $fillable = [
        'nome',
        'sigla',
        'cnpj',
        'responsavel_cargo',
        'responsavel_substituto_cargo',
        'email',
        'telefone',
        'site',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'horario_atendimento',
        'observacao',
    ];

    // O perfil do CRA (só existe um); null enquanto não for cadastrado
    public static function unico(): ?self
    {
        return static::query()->orderBy('cra_perfil_id')->first();
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(CraPerfilHistorico::class, 'cra_perfil_id', 'cra_perfil_id');
    }

    // Unidade operacional do CRA, com responsável e substituto carregados
    public static function unidadeOperacional(): ?UnidadeOperacional
    {
        return UnidadeOperacional::with(['responsavel', 'responsavelSubstituto'])
            ->find(self::UNIDADE_OPERACIONAL_ID);
    }
}
