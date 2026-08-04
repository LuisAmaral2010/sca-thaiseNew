{{-- resources/views/solicitacao_servico/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Solicitação de Serviço #' . $solicitacao->solicitacao_servico_id)

@section('content')
<div class="container">
    <h1>Solicitação de Serviço #{{ $solicitacao->solicitacao_servico_id }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Descrição:</strong> {{ $solicitacao->descricao ?? '-' }}</p>
            <p><strong>Data de solicitação:</strong> {{ optional($solicitacao->data_solicitacao)->format('d/m/Y') ?? '-' }}</p>
            <p><strong>Atividade:</strong> {{ $solicitacao->atividade->nome ?? '-' }}</p>
            <p><strong>Solicitante (matrícula):</strong> {{ $solicitacao->solicitante_matricula ?? '-' }}</p>
        </div>
    </div>

    <h3>Amostras relacionadas</h3>

    @if($solicitacao->amostras && $solicitacao->amostras->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Descrição</th>
                        <th>Validade (dias)</th>
                        <th>Condição de armazenamento</th>
                        <th>Número CRA</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitacao->amostras as $amostra)
                        <tr>
                            <td>{{ $amostra->amostra_id }}</td>
                            <td>{{ $amostra->descricao }}</td>
                            <td>{{ $amostra->validade_dias }}</td>
                            <td>{{ $amostra->condicao_armazenamento }}</td>
                            <td>{{ $amostra->numero_cra }}</td>
                            <td>
                                {{-- Ajuste rotas conforme suas rotas nomeadas --}}
                                <a href="{{ route('amostras.show', $amostra->amostra_id) }}" class="btn btn-sm btn-primary">Ver</a>
                                <a href="{{ route('amostras.edit', $amostra->amostra_id) }}" class="btn btn-sm btn-secondary">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>Nenhuma amostra cadastrada para esta solicitação.</p>
    @endif

    <h3>Ordens de Serviço relacionadas</h3>

    @if($solicitacao->ordemServico && $solicitacao->ordemServico->count())
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Status atual</th>
                        <th>Data status</th>
                        <th>Recebedor (matrícula)</th>
                        <th>Unidade operacional</th>
                        <th>Observação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitacao->ordemServico as $ordem)
                        
                        <tr>
                            <td>{{ $ordem->ordem_servico_id }}</td>
                            <td>{{ $ordem->status_atual }}</td>
                            <td>{{ \Carbon\Carbon::parse($ordem->data_status_atual)->format('d/m/Y') }}</td>
                            
                            <td>{{ $ordem->recebedor_matricula ?? '-' }}</td>
                            <td>{{ $ordem->unidade_operacional_id->nome ?? '-' }}</td>
                        
                            <td>{{ Str::limit($ordem->observacao, 80) }}</td>
                            <td>
                                <a href="{{-- route('ordens.show', $ordem->ordem_servico_id) --}}" class="btn btn-sm btn-primary">Ver</a>
                                <a href="{{-- route('ordens.edit', $ordem->ordem_servico_id) --}}" class="btn btn-sm btn-secondary">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>Nenhuma ordem de serviço vinculada a esta solicitação.</p>
    @endif


    <a href="{{ url()->previous() }}" class="btn btn-light mt-3">Voltar</a>
</div>
@endsection
