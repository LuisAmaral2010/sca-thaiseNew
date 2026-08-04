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

    <div class="sca-card-list">
        @forelse ($solicitacoes_servicos as $solicitacao_servico)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $solicitacao_servico->solicitacao_servico_id }}</span>
                    <span class="sca-card__date">{{ \Carbon\Carbon::parse($solicitacao_servico->data_solicitacao)->format('d/m/Y') }}</span>
                </div>

                <h3 class="sca-card__title">{{ $solicitacao_servico->descricao }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Atividade</dt>
                        <dd>{{ $solicitacao_servico->atividade->titulo ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>Solicitante</dt>
                        <dd>{{ $solicitacao_servico->empregado->nome }} <span class="sca-card__muted">({{ $solicitacao_servico->solicitante_matricula }})</span></dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
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
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $solicitacoes_servicos->links('pagination::bootstrap-5') }}
    </div>
@endsection
