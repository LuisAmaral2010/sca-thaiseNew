<?php

namespace App\Http\Controllers;

use App\Http\Requests\CraPerfilRequest;
use App\Models\CraPerfil;
use App\Models\CraPerfilHistorico;
use App\Models\Empregado;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

// Perfil do CRA (tabela cra_perfil, registro único): consulta, cadastro
// (só enquanto não existe) e edição; toda gravação fica em cra_perfil_historico
class CraPerfilController extends Controller
{
    // Quantidade de alterações exibidas na página
    private const LIMITE_HISTORICO = 20;

    public function show(Request $request)
    {
        $perfil = CraPerfil::unico();

        $historico = $perfil
            ? $perfil->historicos()
                ->latest('created_at')
                ->latest('cra_perfil_historico_id')
                ->limit(self::LIMITE_HISTORICO)
                ->get()
                ->map(fn (CraPerfilHistorico $h) => [
                    'id' => $h->cra_perfil_historico_id,
                    'acao' => $h->acao,
                    'alteracoes' => $h->alteracoes,
                    'usuario' => $h->usuario_nome ?? $h->usuario_matricula,
                    'data' => $h->created_at?->format('d/m/Y H:i'),
                ])
            : [];

        return Inertia::render('Cra/Perfil/Show', [
            'perfil' => $perfil ? [
                ...$perfil->toArray(),
                'created_at_formatted' => $perfil->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $perfil->updated_at?->format('d/m/Y H:i'),
            ] : null,
            'responsaveis' => $this->responsaveis(),
            'historico' => $historico,
            'can' => [
                'create' => $request->user()->can('create', CraPerfil::class),
                'update' => $perfil !== null && $request->user()->can('update', $perfil),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Cra/Perfil/Create', [
            'responsaveis' => $this->responsaveis(),
        ]);
    }

    public function store(CraPerfilRequest $request)
    {
        try {
            $criado = DB::transaction(function () use ($request) {
                // Rechecagem dentro da transação: só existe um perfil do CRA
                if (CraPerfil::query()->lockForUpdate()->count() > 0) {
                    return false;
                }

                $perfil = new CraPerfil($request->validated());
                $alteracoes = $this->alteracoes($perfil);
                $perfil->save();
                $this->registrarHistorico($perfil, CraPerfilHistorico::ACAO_CRIACAO, $alteracoes, $request);

                return true;
            });

            if (!$criado) {
                return redirect()
                    ->route('cra.perfil.show')
                    ->with('error', 'O perfil do CRA já está cadastrado. Use "Editar" para alterá-lo.');
            }

            return redirect()
                ->route('cra.perfil.show')
                ->with('success', 'Perfil do CRA cadastrado com sucesso!');
        } catch (Exception $e) {
            Log::error('Perfil do CRA não cadastrado.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Perfil do CRA não cadastrado!');
        }
    }

    public function edit()
    {
        $perfil = CraPerfil::unico() ?? abort(404);
        $this->authorize('update', $perfil);

        return Inertia::render('Cra/Perfil/Edit', [
            'perfil' => $perfil,
            'responsaveis' => $this->responsaveis(),
        ]);
    }

    public function update(CraPerfilRequest $request)
    {
        try {
            $perfil = CraPerfil::unico() ?? abort(404);
            $perfil->fill($request->validated());
            $alteracoes = $this->alteracoes($perfil);

            if (empty($alteracoes)) {
                return redirect()
                    ->route('cra.perfil.show')
                    ->with('success', 'Nenhuma alteração para salvar.');
            }

            DB::transaction(function () use ($perfil, $alteracoes, $request) {
                $perfil->save();
                $this->registrarHistorico($perfil, CraPerfilHistorico::ACAO_ALTERACAO, $alteracoes, $request);
            });

            return redirect()
                ->route('cra.perfil.show')
                ->with('success', 'Perfil do CRA editado com sucesso!');
        } catch (Exception $e) {
            Log::error('Perfil do CRA não editado.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Perfil do CRA não editado!');
        }
    }

    // Campos alterados (ainda não gravados) com valor anterior e novo;
    // no cadastro, campos deixados em branco não contam como alteração
    private function alteracoes(CraPerfil $perfil): array
    {
        return collect($perfil->getDirty())
            ->except(['created_at', 'updated_at'])
            ->map(fn ($novo, $campo) => [
                'campo' => $campo,
                'anterior' => $perfil->getOriginal($campo),
                'novo' => $novo,
            ])
            ->reject(fn ($a) => $a['anterior'] === null && $a['novo'] === null)
            ->values()
            ->all();
    }

    private function registrarHistorico(CraPerfil $perfil, string $acao, array $alteracoes, Request $request): void
    {
        $usuario = Auth::user();

        $perfil->historicos()->create([
            'acao' => $acao,
            'alteracoes' => $alteracoes,
            'usuario_matricula' => $usuario?->empregado?->matricula,
            'usuario_nome' => $usuario?->empregado?->nome ?? $usuario?->name,
            'ip' => $request->ip(),
        ]);
    }

    // Responsável e substituto lidos da unidade_operacional do CRA (somente leitura)
    private function responsaveis(): array
    {
        $unidade = CraPerfil::unidadeOperacional();

        return [
            'responsavel' => $this->empregado($unidade?->responsavel_matricula, $unidade?->responsavel),
            'substituto' => $this->empregado($unidade?->responsavel_substituto_matricula, $unidade?->responsavelSubstituto),
        ];
    }

    private function empregado(?string $matricula, ?Empregado $empregado): ?array
    {
        if (!$matricula) {
            return null;
        }

        return ['matricula' => $matricula, 'nome' => $empregado?->nome];
    }
}
