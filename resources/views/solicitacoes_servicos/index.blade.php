@extends('layouts.app')

@section('title', 'Solicitações de Serviço')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Solicitações de Serviço</h1>
            <p class="sca-page-header__subtitle">Acompanhe e gerencie as solicitações cadastradas.</p>
        </div>
        <a href="{{ route('solicitacoes_servicos.create') }}" class="sca-btn sca-btn--primary">Cadastrar</a>
    </div>

    <x-alert />

    <form method="GET" action="{{ route('solicitacoes_servicos.index') }}" class="sca-filter-bar">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="sca-field">
            <label class="sca-field__label" for="data_inicial">Data inicial</label>
            <input type="date" id="data_inicial" name="data_inicial" class="sca-input" value="{{ $dataInicial }}">
        </div>

        <div class="sca-field">
            <label class="sca-field__label" for="data_final">Data final</label>
            <input type="date" id="data_final" name="data_final" class="sca-input" value="{{ $dataFinal }}">
        </div>

        <button type="submit" class="sca-btn sca-btn--secondary sca-btn--sm">Filtrar</button>

        @if($dataInicial || $dataFinal)
            <a href="{{ route('solicitacoes_servicos.index', ['sort' => $sort, 'direction' => $direction]) }}" class="sca-link">Limpar filtro</a>
        @endif
    </form>

    @if($solicitacoes_servicos->count())
        <div class="sca-panel">
            <table class="sca-table">
                <thead>
                    <tr>
                        <x-sortable-th column="solicitacao_servico_id" :sort="$sort" :direction="$direction">#</x-sortable-th>
                        <x-sortable-th column="data_solicitacao" :sort="$sort" :direction="$direction">Data</x-sortable-th>
                        <x-sortable-th column="descricao" :sort="$sort" :direction="$direction">Descrição</x-sortable-th>
                        <th>Atividade</th>
                        <th>Solicitante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitacoes_servicos as $solicitacao_servico)
                        <tr>
                            <td><span class="sca-badge">#{{ $solicitacao_servico->solicitacao_servico_id }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($solicitacao_servico->data_solicitacao)->format('d/m/Y') }}</td>
                            <td>{{ $solicitacao_servico->descricao }}</td>
                            <td>{{ $solicitacao_servico->atividade->titulo ?? '—' }}</td>
                            <td>{{ $solicitacao_servico->empregado->nome }} <span class="sca-card__muted">({{ $solicitacao_servico->solicitante_matricula }})</span></td>
                            <td>
                                <a href="{{ route('solicitacoes_servicos.show', $solicitacao_servico) }}" class="sca-btn sca-btn--outline sca-btn--sm">Detalhes</a>

                                @can('index-solicitacao_servico')
                                    <a href="{{ route('solicitacao_servico.index', ['solicitacao_servico' => $solicitacao_servico->id]) }}" class="sca-link">Turmas</a>
                                @endcan

                                @can('show-solicitacao_servico')
                                    <a href="{{ route('solicitacoes_servicos.show', ['solicitacao_servico' => $solicitacao_servico->id]) }}" class="sca-link">Visualizar</a>
                                @endcan

                                @can('edit-solicitacao_servico')
                                    <a href="{{ route('solicitacoes_servicos.edit', ['solicitacao_servico' => $solicitacao_servico->id]) }}" class="sca-link">Editar</a>
                                @endcan

                                @can('destroy-solicitacao_servico')
                                    <form action="{{ route('solicitacao_servico.destroy', ['solicitacao_servico' => $solicitacao_servico->id]) }}" method="POST" class="sca-card__delete-form">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="sca-link sca-link--danger" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="sca-empty">Nenhum registro encontrado!</div>
    @endif

    <div class="sca-pagination">
        {{ $solicitacoes_servicos->links('pagination::bootstrap-5') }}
    </div>
@endsection
