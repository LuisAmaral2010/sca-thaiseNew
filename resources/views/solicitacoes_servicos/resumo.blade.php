@extends('layouts.app')

@section('title', 'Resumo da Solicitação #' . ($solicitacao->solicitacao_servico_id ?? $solicitacao->id))

@section('content')
<div class="sca-page-header">
    <div>
        <h1 class="sca-page-header__title">Resumo da Solicitação #{{ $solicitacao->solicitacao_servico_id ?? $solicitacao->id }}</h1>
    </div>
</div>

<div class="sca-panel">
    <div class="sca-panel__header">Dados da Solicitação</div>
    <div class="sca-panel__body">
        <dl class="sca-card__meta">
            <div>
                <dt>Descrição</dt>
                <dd>{{ $solicitacao->descricao }}</dd>
            </div>
            <div>
                <dt>Atividade ID</dt>
                <dd>{{ $solicitacao->atividade_id }}</dd>
            </div>
            <div>
                <dt>Solicitante (matrícula)</dt>
                <dd>{{ $solicitacao->solicitante_matricula }}</dd>
            </div>
            <div>
                <dt>Data solicitação</dt>
                <dd>{{ $solicitacao->data_solicitacao }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="sca-panel">
    <div class="sca-panel__header">Relação de Amostras x Unidades Operacionais / Serviços</div>
    <div class="sca-panel__body">
        <table class="sca-table mb-0">
            <thead>
                <tr>
                    <th style="width: 35%;">Amostra</th>
                    <th style="width: 25%;">Unidade Operacional</th>
                    <th>Serviço</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($amostras) || count($amostras) === 0)
                    <tr>
                        <td colspan="3" class="text-center sca-field__hint">Nenhuma amostra informada.</td>
                    </tr>
                @else
                    @foreach($amostras as $amostra)
                        @php
                            // calcular total de linhas para rowSpan (soma serviços de todas as unidades)
                            $totalLinhas = array_reduce($servicosPorUnidade ?? [], function($carry, $arr) {
                                return $carry + count($arr);
                            }, 0);
                            $linhaAtual = 0;
                        @endphp

                        @foreach($servicosPorUnidade ?? [] as $unidadeNome => $servicos)
                            @foreach($servicos as $idx => $s)
                                <tr>
                                    @if($linhaAtual === 0 && $idx === 0)
                                        <td rowspan="{{ $totalLinhas }}">
                                            <strong>{{ $amostra['descricao'] ?? ($amostra->descricao ?? '—') }}</strong><br>
                                            <small class="sca-card__muted">
                                                Validade: {{ $amostra['validade_dias'] ?? ($amostra->validade_dias ?? '—') }} dias<br>
                                                Condição: {{ $amostra['condicao_armazenamento'] ?? ($amostra->condicao_armazenamento ?? '—') }}
                                            </small>
                                        </td>
                                    @endif

                                    @if($idx === 0)
                                        <td rowspan="{{ count($servicos) }}">{{ $unidadeNome }}</td>
                                    @endif

                                    <td>
                                        {{ $s['descricao'] ?? $s->descricao ?? '—' }}
                                        @if(!empty($s['tipo_servico'] ?? $s->tipo_servico ?? null))
                                            <small class="sca-card__muted">({{ $s['tipo_servico'] ?? $s->tipo_servico }})</small>
                                        @endif
                                    </td>
                                </tr>
                                @php $linhaAtual++; @endphp
                            @endforeach
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between">
    <a href="{{ route('solicitacoes_servicos.create') }}" class="sca-btn sca-btn--outline">Voltar</a>
</div>
@endsection
