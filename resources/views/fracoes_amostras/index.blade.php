@extends('layouts.app')

@section('title', 'Frações Amostras')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Frações Amostras</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($fracoes_amostras as $fracao_amostra)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $fracao_amostra->fracao_amostra_id }}</span>
                    <span class="sca-card__date">{{ $fracao_amostra->data_status_atual }}</span>
                </div>

                <h3 class="sca-card__title">{{ $fracao_amostra->status_atual }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Amostra</dt>
                        <dd>{{ $fracao_amostra->amostra_id }}</dd>
                    </div>
                    <div>
                        <dt>Serviço</dt>
                        <dd>{{ $fracao_amostra->servico_id }}</dd>
                    </div>
                    <div>
                        <dt>Ordem de Serviço</dt>
                        <dd>{{ $fracao_amostra->ordem_servico_id }}</dd>
                    </div>
                    <div>
                        <dt>Responsável</dt>
                        <dd>{{ $fracao_amostra->responsavel_execucao_matricula }}</dd>
                    </div>
                </dl>

                @if ($fracao_amostra->observacao)
                    <p class="sca-card__description">{{ $fracao_amostra->observacao }}</p>
                @endif

            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $fracoes_amostras->links('pagination::bootstrap-5') }}
    </div>
@endsection
