<?php

namespace App\Http\Requests;

use App\Models\ClienteExterno;
use App\Rules\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Validação do cadastro e da edição de cliente externo (pessoa física ou jurídica)
class ClienteExternoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Documento, CEP e telefone são gravados só com números; campos
    // específicos do outro tipo de pessoa são descartados
    protected function prepareForValidation(): void
    {
        $somenteNumeros = fn ($valor) => $valor === null ? null : (preg_replace('/\D/', '', (string) $valor) ?: null);

        $dados = [
            'cpf_cnpj' => $somenteNumeros($this->input('cpf_cnpj')),
            'cep' => $somenteNumeros($this->input('cep')),
            'telefone' => $somenteNumeros($this->input('telefone')),
            'uf' => $this->filled('uf') ? mb_strtoupper(trim($this->input('uf'))) : null,
            'is_ativo' => $this->boolean('is_ativo'),
        ];

        if ($this->input('tipo_pessoa') === ClienteExterno::PESSOA_FISICA) {
            $dados += ['nome_fantasia' => null, 'inscricao_estadual' => null, 'responsavel_nome' => null];
        } elseif ($this->input('tipo_pessoa') === ClienteExterno::PESSOA_JURIDICA) {
            $dados += ['rg' => null, 'data_nascimento' => null];
        }

        $this->merge($dados);
    }

    public function rules(): array
    {
        $tipo = $this->input('tipo_pessoa');
        $clienteExterno = $this->route('cliente_externo');

        return [
            'tipo_pessoa' => ['required', Rule::in([ClienteExterno::PESSOA_FISICA, ClienteExterno::PESSOA_JURIDICA])],
            'nome' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cpf_cnpj' => [
                'required',
                new CpfCnpj(in_array($tipo, ['F', 'J'], true) ? $tipo : null),
                Rule::unique('cliente_externo', 'cpf_cnpj')->ignore($clienteExterno?->cliente_externo_id, 'cliente_externo_id'),
            ],
            'rg' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'inscricao_estadual' => ['nullable', 'string', 'max:20'],
            'responsavel_nome' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'digits_between:10,11'],
            'cep' => ['nullable', 'digits:8'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'size:2', 'alpha'],
            'observacao' => ['nullable', 'string', 'max:2000'],
            'is_ativo' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_pessoa' => 'tipo de pessoa',
            'nome' => $this->input('tipo_pessoa') === ClienteExterno::PESSOA_JURIDICA ? 'razão social' : 'nome completo',
            'nome_fantasia' => 'nome fantasia',
            'cpf_cnpj' => $this->input('tipo_pessoa') === ClienteExterno::PESSOA_JURIDICA ? 'CNPJ' : 'CPF',
            'rg' => 'RG',
            'data_nascimento' => 'data de nascimento',
            'inscricao_estadual' => 'inscrição estadual',
            'responsavel_nome' => 'nome do responsável',
            'email' => 'e-mail',
            'telefone' => 'telefone',
            'cep' => 'CEP',
            'uf' => 'UF',
            'observacao' => 'observação',
        ];
    }

    public function messages(): array
    {
        return [
            'cpf_cnpj.unique' => 'Já existe um cliente externo cadastrado com este :attribute.',
            'telefone.digits_between' => 'O telefone deve ter DDD + número (10 ou 11 dígitos).',
            'cep.digits' => 'O CEP deve ter 8 dígitos.',
        ];
    }
}
