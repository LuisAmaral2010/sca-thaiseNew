<?php

namespace App\Http\Controllers;

use App\Models\Laudo;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaboratorioGravarLaudoController extends Controller
{
    // Unidades operacionais em que o usuário logado tem permissão de
    // laboratório (permissao_unidade_operacional.usuario_matricula)
    private function unidadesOperacionaisPermitidas(): array
    {
        $matricula = auth()->user()?->empregado?->matricula;

        if (!$matricula) {
            return [];
        }

        return PermissaoUnidadeOperacional::whereNotNull('unidade_operacional_id')
            ->where('usuario_matricula', $matricula)
            ->pluck('unidade_operacional_id')
            ->all();
    }

    // Tela de filtro (data inicial/final e unidade operacional) e listagem
    // dos laudos já emitidos pelo laboratório, disponíveis para gravação
    public function index(Request $request)
    {
        $validated = $request->validate([
            'data_inicial' => ['nullable', 'date'],
            'data_final' => ['nullable', 'date'],
            'unidade_operacional_id' => ['nullable', 'integer'],
        ]);

        $unidadesPermitidas = $this->unidadesOperacionaisPermitidas();

        $unidadesOperacionais = UnidadeOperacional::whereIn('unidade_operacional_id', $unidadesPermitidas)
            ->orderBy('nome')
            ->get(['unidade_operacional_id', 'nome']);

        $unidadeOperacionalId = $validated['unidade_operacional_id'] ?? null;

        abort_if($unidadeOperacionalId && !in_array($unidadeOperacionalId, $unidadesPermitidas), 403);

        $laudos = $unidadeOperacionalId
            ? Laudo::query()
                ->join('ordem_servico', 'ordem_servico.ordem_servico_id', '=', 'laudo.ordem_servico_id')
                ->leftJoin('unidade_operacional', 'unidade_operacional.unidade_operacional_id', '=', 'ordem_servico.unidade_operacional_id')
                ->where('ordem_servico.unidade_operacional_id', $unidadeOperacionalId)
                ->whereNotNull('laudo.numero_laudo')
                ->when($validated['data_inicial'] ?? null, fn ($query, $data) => $query->whereDate('laudo.data_emissao', '>=', $data))
                ->when($validated['data_final'] ?? null, fn ($query, $data) => $query->whereDate('laudo.data_emissao', '<=', $data))
                ->orderByDesc('laudo.numero_laudo')
                ->get([
                    'laudo.laudo_id',
                    'laudo.numero_laudo',
                    'laudo.data_emissao',
                    'laudo.status_atual',
                    'laudo.ordem_servico_id',
                    'unidade_operacional.nome as unidade_operacional',
                ])
            : collect();

        return Inertia::render('Laboratorio/GravarLaudo', [
            'unidadesOperacionais' => $unidadesOperacionais,
            'filtros' => $request->only(['data_inicial', 'data_final', 'unidade_operacional_id']),
            'laudos' => $laudos,
        ]);
    }
}
