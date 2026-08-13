@extends('layouts.app')

@section('title', 'Resumo da Solicitação #' . ($solicitacao->solicitacao_servico_id ?? $solicitacao->id))

@section('content')
<div class="sca-page-header">
    <div>
        <h1 class="sca-page-header__title">Resumo da Solicitação #{{ $solicitacao->solicitacao_servico_id ?? $solicitacao->id }}</h1>
    </div>
</div>

{{-- Mesmo layout visual do modal #modalResumo (create.blade.php), como página normal --}}
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">
            RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS
        </h5>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <h6>Dados da Solicitação</h6>
            <table class="sca-table">
                <tbody>
                    <tr>
                        <th style="width: 180px;">Atividade ID</th>
                        <td>{{ $solicitacao->atividade_id }}</td>
                    </tr>
                    <tr>
                        <th>Descrição</th>
                        <td>{{ $solicitacao->descricao }}</td>
                    </tr>
                    <tr>
                        <th>Solicitante</th>
                        <td>{{ $solicitacao->solicitante_matricula }}</td>
                    </tr>
                    <tr>
                        <th>Data da Solicitação</th>
                        <td>{{ $solicitacao->data_solicitacao }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

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

    <div class="modal-footer">
        <a href="{{ route('solicitacoes_servicos.create') }}" class="sca-btn sca-btn--outline">
            Voltar
        </a>
        <a href="{{ route('solicitacoes_servicos.index') }}" class="sca-btn sca-btn--primary">
            Finalizar
        </a>
    </div>
</div>
@endsection
