{{-- resources/views/solicitacao_servico/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Solicitação de Serviço #' . $solicitacao->solicitacao_servico_id)

@section('content')
<div class="sca-page-header">
    <div>
        <h1 class="sca-page-header__title">Solicitação de Serviço #{{ $solicitacao->solicitacao_servico_id }}</h1>
    </div>
</div>

<div class="sca-panel">
    <div class="sca-panel__header">Dados da Solicitação</div>
    <div class="sca-panel__body">
        <dl class="sca-card__meta">
            <div>
                <dt>Descrição</dt>
                <dd>{{ $solicitacao->descricao ?? '-' }}</dd>
            </div>
            <div>
                <dt>Data de solicitação</dt>
                <dd>{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt>Atividade</dt>
                <dd>{{ $solicitacao->atividade->nome ?? '-' }}</dd>
            </div>
            <div>
                <dt>Solicitante (matrícula)</dt>
                <dd>{{ $solicitacao->solicitante_matricula ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="sca-panel">
    <div class="sca-panel__header">Amostras relacionadas</div>
    <div class="sca-panel__body">
        @if($solicitacao->amostras && $solicitacao->amostras->count())
            <table class="sca-table">
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
                                <a href="{{ route('amostras.show', $amostra->amostra_id) }}" class="sca-link">Ver</a>
                                <a href="{{ route('amostras.edit', $amostra->amostra_id) }}" class="sca-link">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="sca-empty">Nenhuma amostra cadastrada para esta solicitação.</div>
        @endif
    </div>
</div>

<div class="sca-panel">
    <div class="sca-panel__header">Ordens de Serviço relacionadas</div>
    <div class="sca-panel__body">
        @if($solicitacao->ordemServico && $solicitacao->ordemServico->count())
            <table class="sca-table">
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
                                <a href="{{-- route('ordens.show', $ordem->ordem_servico_id) --}}" class="sca-link">Ver</a>
                                <a href="{{-- route('ordens.edit', $ordem->ordem_servico_id) --}}" class="sca-link">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="sca-empty">Nenhuma ordem de serviço vinculada a esta solicitação.</div>
        @endif
    </div>
</div>

<a href="{{ url()->previous() }}" class="sca-btn sca-btn--outline">Voltar</a>
@endsection
