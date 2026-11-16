<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Cliente externo do CRA: pessoa física (tipo_pessoa = F) ou jurídica (J)
class ClienteExterno extends Model
{
    public const PESSOA_FISICA = 'F';
    public const PESSOA_JURIDICA = 'J';

    protected $table = 'cliente_externo';

    protected $primaryKey = 'cliente_externo_id';

    protected $fillable = [
        'tipo_pessoa',
        'nome',
        'nome_fantasia',
        'cpf_cnpj',
        'rg',
        'data_nascimento',
        'inscricao_estadual',
        'responsavel_nome',
        'email',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'observacao',
        'is_ativo',
    ];

    protected $casts = [
        'data_nascimento' => 'date:Y-m-d',
        'is_ativo' => 'boolean',
    ];

    // Requisições externas feitas para este cliente
    public function solicitacoesServicos()
    {
        return $this->hasMany(SolicitacaoServico::class, 'cliente_externo_id', 'cliente_externo_id');
    }

    public function isPessoaFisica(): bool
    {
        return $this->tipo_pessoa === self::PESSOA_FISICA;
    }

    // CPF 000.000.000-00 ou CNPJ 00.000.000/0000-00
    public function getCpfCnpjFormatadoAttribute(): ?string
    {
        $doc = $this->cpf_cnpj;

        if (strlen((string) $doc) === 11) {
            return vsprintf('%s.%s.%s-%s', [substr($doc, 0, 3), substr($doc, 3, 3), substr($doc, 6, 3), substr($doc, 9, 2)]);
        }

        if (strlen((string) $doc) === 14) {
            return vsprintf('%s.%s.%s/%s-%s', [substr($doc, 0, 2), substr($doc, 2, 3), substr($doc, 5, 3), substr($doc, 8, 4), substr($doc, 12, 2)]);
        }

        return $doc;
    }
}
