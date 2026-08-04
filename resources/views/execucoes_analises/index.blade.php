@extends('layouts.app')

@section('title', 'Execuções de Análises')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Execuções de Análises</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($execucoes_analises as $execucao_analise)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $execucao_analise->execucao_analise_id }}</span>
                    <span class="sca-card__date">{{ $execucao_analise->created_at }}</span>
                </div>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Fração Amostra</dt>
                        <dd>{{ $execucao_analise->fracao_amostra_id }}</dd>
                    </div>
                    <div>
                        <dt>Laudo</dt>
                        <dd>{{ $execucao_analise->laudo_id }}</dd>
                    </div>
                    <div>
                        <dt>Ordem de Serviço</dt>
                        <dd>{{ $execucao_analise->ordem_servico_id }}</dd>
                    </div>
                    <div>
                        <dt>Serviço</dt>
                        <dd>{{ $execucao_analise->servico_id }}</dd>
                    </div>
                    <div>
                        <dt>Concluído</dt>
                        <dd>{{ $execucao_analise->is_concluido ? 'Sim' : 'Não' }} <span class="sca-card__muted">{{ $execucao_analise->data_conclusao }}</span></dd>
                    </div>
                    <div>
                        <dt>Cancelado</dt>
                        <dd>{{ $execucao_analise->is_cancelado ? 'Sim' : 'Não' }} <span class="sca-card__muted">{{ $execucao_analise->data_cancelamento }}</span></dd>
                    </div>
                </dl>

                @if ($execucao_analise->observacao)
                    <p class="sca-card__description">{{ $execucao_analise->observacao }}</p>
                @endif

            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $execucoes_analises->links('pagination::bootstrap-5') }}
    </div>
@endsection
