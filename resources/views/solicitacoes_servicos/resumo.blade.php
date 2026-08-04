@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Resumo da Solicitação #{{ $solicitacao->solicitacao_servico_id ?? $solicitacao->id }}</h1>

    <div class="card mb-3">
        <div class="card-header">Dados da Solicitação</div>
        <div class="card-body">
            <p><strong>Descrição:</strong> {{ $solicitacao->descricao }}</p>
            <p><strong>Atividade ID:</strong> {{ $solicitacao->atividade_id }}</p>
            <p><strong>Solicitante (matrícula):</strong> {{ $solicitacao->solicitante_matricula }}</p>
            <p><strong>Data solicitação:</strong> {{ $solicitacao->data_solicitacao }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Amostra</th>
                            <th style="width: 25%;">Unidade Operacional</th>
                            <th>Serviço</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($amostras) || count($amostras) === 0)
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nenhuma amostra informada.</td>
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
                                                    <small>
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
                                                    <small class="text-muted"> ({{ $s['tipo_servico'] ?? $s->tipo_servico }})</small>
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
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('solicitacoes_servicos.create') }}" class="btn btn-secondary">Voltar</a>
<!--
        <form action="{{-- route('solicitacoes_servicos.finalizar', $solicitacao->solicitacao_servico_id ?? $solicitacao->id) --}}" method="POST" onsubmit="return confirm('Confirma finalizar a solicitação?');">
            @csrf
            @method('POST')
            <button type="submit" class="btn btn-primary">Finalizar</button>
        </form>
-->
    </div>
</div>
@endsection
