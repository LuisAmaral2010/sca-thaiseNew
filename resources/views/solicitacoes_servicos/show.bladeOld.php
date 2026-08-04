@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Resumo ! da Solicitação #{{ $solicitacao->solicitacao_servico_id ?? $solicitacao->id }}</h1>

    <div class="card mb-3">
        <div class="card-header">Dados da Solicitação</div>
        <div class="card-body">
            <p><strong>Descrição:</strong> {!! nl2br(e($solicitacao->descricao)) !!}</p>
            <p><strong>Atividade:</strong> {{ optional($solicitacao->atividade)->titulo ?? ($solicitacao->atividade_id ?? '—') }}</p>
            <p><strong>Solicitante (matrícula):</strong> {{ $solicitacao->solicitante_matricula ?? '—' }}</p>
            <p><strong>Data solicitação:</strong> {{ optional($solicitacao->data_solicitacao)->format('d/m/Y H:i') ?? $solicitacao->data_solicitacao ?? '—' }}</p>
        </div>
    </div>

    {{-- Amostras --}}
    <div class="card mb-3">
        <div class="card-header">Amostras</div>
        <div class="card-body p-0">
            @if(!empty($amostras) && count($amostras) > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Descrição</th>
                                <th>Validade (dias)</th>
                                <th>Condição Armazenamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($amostras as $a)
                                <tr>
                                    <td>{{ $a['descricao'] ?? '—' }}</td>
                                    <td>{{ $a['validade_dias'] ?? '—' }}</td>
                                    <td>{{ $a['condicao_armazenamento'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-3 text-muted">Nenhuma amostra cadastrada.</div>
            @endif
        </div>
    </div>

    {{-- Serviços por Unidade --}}
    <div class="card mb-3">
        <div class="card-header">Serviços por Unidade Operacional</div>
        <div class="card-body">
            @if(!empty($servicosPorUnidade) && count($servicosPorUnidade) > 0)
                @foreach($servicosPorUnidade as $unidade => $servicos)
                    <div class="mb-3">
                        <h6 class="mb-1">{{ $unidade }}</h6>
                        <ul class="mb-0">
                            @foreach($servicos as $s)
                                <li>
                                    @if(is_array($s))
                                        {{ $s['descricao'] ?? '—' }} @if(!empty($s['tipo_servico'])) <small class="text-muted">({{ $s['tipo_servico'] }})</small> @endif
                                    @else
                                        {{ $s }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div class="text-muted">Nenhum serviço vinculado.</div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('solicitacoes_servicos.index') }}" class="btn btn-secondary">Voltar</a>
        <a href="{{ route('solicitacoes_servicos.create') }}" class="btn btn-primary">Nova Solicitação</a>
    </div>
</div>
@endsection
