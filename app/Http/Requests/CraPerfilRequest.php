<?php

namespace App\Http\Requests;

use App\Models\CraPerfil;
use App\Rules\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

// Validação dos dados institucionais do CRA
class CraPerfilRequest extends FormRequest
{
    // Só USUARIO_CRA (CraPerfilPolicy); cadastro só enquanto o perfil não existe
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()?->can('create', CraPerfil::class) ?? false;
        }

        $perfil = CraPerfil::unico();

        return $perfil !== null && ($this->user()?->can('update', $perfil) ?? false);
    }

    // CNPJ, CEP e telefone são gravados só com números
    protected function prepareForValidation(): void
    {
        $somenteNumeros = fn ($valor) => $valor === null ? null : (preg_replace('/\D/', '', (string) $valor) ?: null);

        $this->merge([
            'cnpj' => $somenteNumeros($this->input('cnpj')),
            'cep' => $somenteNumeros($this->input('cep')),
            'telefone' => $somenteNumeros($this->input('telefone')),
            'uf' => $this->filled('uf') ? mb_strtoupper(trim($this->input('uf'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['nullable', 'string', 'max:20'],
            'cnpj' => ['nullable', new CpfCnpj('J')],
            'responsavel_cargo' => ['nullable', 'string', 'max:100'],
            'responsavel_substituto_cargo' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'digits_between:10,11'],
            'site' => ['nullable', 'url', 'max:255'],
            'cep' => ['nullable', 'digits:8'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'size:2', 'alpha'],
            'horario_atendimento' => ['nullable', 'string', 'max:255'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'sigla' => 'sigla',
            'cnpj' => 'CNPJ',
            'responsavel_cargo' => 'cargo do responsável',
            'responsavel_substituto_cargo' => 'cargo do responsável substituto',
            'email' => 'e-mail',
            'site' => 'site',
            'cep' => 'CEP',
            'uf' => 'UF',
            'horario_atendimento' => 'horário de atendimento',
            'observacao' => 'observação',
        ];
    }

    public function messages(): array
    {
        return [
            'telefone.digits_between' => 'O telefone deve ter DDD + número (10 ou 11 dígitos).',
            'cep.digits' => 'O CEP deve ter 8 dígitos.',
            'site.url' => 'Informe o site com http:// ou https://.',
        ];
    }
}
