<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmostraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('amostra')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'max:150'],
            'solicitacao_id' => ['required', 'integer', 'exists:solicitacao_servico,solicitacao_servico_id'],
            'validade_dias' => ['required', 'integer', 'min:0'],
            'condicao_armazenamento' => ['required', 'string', 'max:50'],
            'numero_cra' => ['required', 'integer'],
        ];
    }
}
