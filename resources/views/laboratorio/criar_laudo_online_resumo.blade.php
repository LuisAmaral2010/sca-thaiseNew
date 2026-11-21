{{-- resources/views/laboratorio/criar_laudo_online_resumo.blade.php --}}
{{-- Aberta pelo botão "Preencher laudo" de /laboratorio/criar-laudo-online:
     mesmas duas primeiras tabelas de solicitacoes_servicos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Preencher laudo - Ordem de Serviço #' . $ordem->ordem_servico_id)

@section('hide_navbar', '1')
@section('use_sidebar', '1')

@section('content')
<div class="sca-page-header">
    <div>
        <h1 class="sca-page-header__title">
            Solicitação de Serviço {{ $solicitacao->numero_solicitacao_servico ?: '#' . $solicitacao->solicitacao_servico_id }}
        </h1>
    </div>
</div>

<x-alert />

{{-- As linhas marcadas vão para selecionar(): laudo_criado = true e abre o editor só com elas --}}
<form method="POST"
      action="{{ route('laboratorio.criar-laudo-online.selecionar', $ordem->ordem_servico_id) }}"
      id="formSelecao"
      class="modal-content mb-4">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title">
            RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS
        </h5>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <h6>Dados da Solicitação</h6>
            <div class="table-responsive">
            <table class="sca-table">
                <tbody>
                    {{-- Requisição externa não tem atividade --}}
                    @unless ($solicitacao->solicitante_externo_nome)
                        <tr>
                            <th style="width: 180px;">Atividade</th>
                            <td>{{ $solicitacao->atividade->titulo ?? '—' }}</td>
                        </tr>
                    @endunless
                    <tr>
                        <th style="width: 180px;">Descrição</th>
                        <td>{!! nl2br(e($solicitacao->descricao ?? '—')) !!}</td>
                    </tr>
                    @if ($solicitacao->solicitante_externo_nome)
                        <tr>
                            <th>Solicitante externo</th>
                            <td>{{ $solicitacao->solicitante_externo_nome }}</td>
                        </tr>
                        <tr>
                            <th>Cadastrado por</th>
                            <td>{{ $solicitacao->empregado->nome ?? $solicitacao->solicitante_matricula ?? '—' }}</td>
                        </tr>
                    @else
                        <tr>
                            <th>Solicitante</th>
                            <td>{{ $solicitacao->empregado->nome ?? $solicitacao->solicitante_matricula ?? '—' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Data da Solicitação</th>
                        <td>{{ $solicitacao->data_solicitacao ? \Carbon\Carbon::parse($solicitacao->data_solicitacao)->format('d/m/Y') : '—' }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <div class="sca-panel">
            <div class="sca-panel__header">
                Relação de Amostras x Unidades Operacionais / Serviços
            </div>
            <div class="sca-panel__body">
                <div class="table-responsive">
                <table class="sca-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Amostra</th>
                            <th style="width: 24%;">Unidade Operacional</th>
                            <th style="width: 16%;">Status da Fração</th>
                            <th>Serviço</th>
                            <th style="width: 1%;" class="text-center">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="selecionarTodas"
                                       title="Selecionar todas"
                                       aria-label="Selecionar todas">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resumo as $item)
                            @php $primeiraLinha = true; @endphp

                            @foreach($item['fracoes'] as $indiceFracao => $fracao)
                                @php $servicos = $fracao['servicos']; @endphp

                                @foreach($servicos->isEmpty() ? [null] : $servicos as $indiceServico => $servico)
                                    <tr>
                                        {{-- 1ª coluna: Amostra (apenas na primeira linha desta amostra) --}}
                                        @if($primeiraLinha)
                                            <td rowspan="{{ $item['total_linhas'] }}">
                                                <strong>{{ $item['amostra']?->descricao ?: '—' }}</strong><br>
                                                <small>
                                                    Validade: {{ $item['amostra']?->validade_dias ?: '—' }} dias<br>
                                                    Condição: {{ $item['amostra']?->condicao_armazenamento ?: '—' }}
                                                </small>
                                            </td>
                                            @php $primeiraLinha = false; @endphp
                                        @endif

                                        {{-- 2ª coluna: Unidade Operacional (mesclada por fração) --}}
                                        @if($indiceServico === 0)
                                            <td rowspan="{{ max(1, $servicos->count()) }}">
                                                Fração {{ $indiceFracao + 1 }}: {{ $fracao['unidade'] }}
                                            </td>

                                            {{-- 3ª coluna: Status da fração (fracao_amostra.status_atual) --}}
                                            <td rowspan="{{ max(1, $servicos->count()) }}">
                                                @if($fracao['status'])
                                                    <span class="sca-badge">{{ str_replace('_', ' ', $fracao['status']) }}</span>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endif

                                        {{-- 4ª coluna: Serviço --}}
                                        <td>
                                            @if($servico)
                                                {{ $servico['descricao'] }}
                                                @if($servico['tipo'])
                                                    <small class="sca-card__muted" style="margin-left: 0.25rem;">({{ $servico['tipo'] }})</small>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- 5ª coluna: seleção da linha (desabilitada quando não há serviço) --}}
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   name="execucoes_analises[]"
                                                   data-selecao-linha
                                                   value="{{ $servico['execucao_analise_id'] ?? '' }}"
                                                   aria-label="Selecionar {{ $servico['descricao'] ?? 'linha' }}"
                                                   @checked($servico['marcada'] ?? false)
                                                   @disabled(!$servico)>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="text-center sca-field__hint">
                                    Nenhuma amostra informada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        {{-- Toggle switch abaixo das tabelas --}}
        <div class="mt-3 d-flex align-items-center gap-2">
            <label for="discriminar_fracao_amostra" class="mb-0" style="cursor: pointer;">Discriminar Fração Amostra</label>
            <label class="switch">
                {{-- Desligado: o editor terá um único campo de resultado para todas as frações --}}
                <input type="hidden" name="discriminar_fracao_amostra" value="0">
                <input type="checkbox" id="discriminar_fracao_amostra" name="discriminar_fracao_amostra" value="1" role="switch" @checked($discriminar)>
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="modal-footer">
        <a href="{{ route('laboratorio.criar-laudo-online', ['unidade_operacional_id' => $ordem->unidade_operacional_id]) }}"
           class="sca-btn sca-btn--outline">Voltar</a>

        <button type="submit" class="sca-btn sca-btn--primary">Preencher resultados</button>
    </div>

</form>

<style>
    /* Toggle switch: <label class="switch"><input type="checkbox"><span class="slider round"></span></label> */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        margin: 0;
        vertical-align: middle;
    }
    .switch input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        inset: 0;
        cursor: pointer;
        background-color: var(--sca-border, #ccc);
        transition: background-color 0.2s;
    }
    .slider::before {
        content: "";
        position: absolute;
        left: 3px;
        bottom: 3px;
        width: 18px;
        height: 18px;
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
    }
    .switch input:checked + .slider {
        background-color: var(--sca-primary, #2196f3);
    }
    .switch input:checked + .slider::before {
        transform: translateX(20px);
    }
    .switch input:focus-visible + .slider {
        outline: 2px solid var(--sca-primary, #2196f3);
        outline-offset: 2px;
    }
    .switch input:disabled + .slider {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .slider.round {
        border-radius: 24px;
    }
    .slider.round::before {
        border-radius: 50%;
    }
    @media (prefers-reduced-motion: reduce) {
        .slider, .slider::before { transition: none; }
    }
</style>

<script>
(function () {
    // "Selecionar todas" no cabeçalho: marca/desmarca as linhas habilitadas
    // e fica indeterminado quando só parte delas está marcada
    const todas = document.getElementById('selecionarTodas');
    const linhas = [...document.querySelectorAll('[data-selecao-linha]:not(:disabled)')];

    // "Preencher resultados" só segue com pelo menos uma linha marcada
    document.getElementById('formSelecao').addEventListener('submit', e => {
        if (!linhas.some(c => c.checked)) {
            e.preventDefault();
            alert('Marcar pelo menos uma das Frações Amostra');
        }
    });

    if (!linhas.length) {
        todas.disabled = true;
        return;
    }

    function atualizar() {
        const marcadas = linhas.filter(c => c.checked).length;
        todas.checked = marcadas === linhas.length;
        todas.indeterminate = marcadas > 0 && marcadas < linhas.length;
    }

    todas.addEventListener('change', () => {
        linhas.forEach(c => { c.checked = todas.checked; });
        atualizar();
    });

    linhas.forEach(c => c.addEventListener('change', atualizar));

    // O navegador pode restaurar marcações ao voltar para a página
    atualizar();
})();
</script>
@endsection
