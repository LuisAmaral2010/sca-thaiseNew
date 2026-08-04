@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Serviços</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($servicos as $servico)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $servico->servico_id }}</span>
                    <span class="sca-card__date">{{ $servico->data_cadastro }}</span>
                </div>

                <h3 class="sca-card__title">{{ $servico->tipo_servico }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Ativo</dt>
                        <dd>{{ $servico->is_ativo ? 'Sim' : 'Não' }}</dd>
                    </div>
                    <div>
                        <dt>Unidade Operacional</dt>
                        <dd>{{ $servico->unidade_operacional_id }}</dd>
                    </div>
                </dl>

                @if ($servico->descricao)
                    <p class="sca-card__description">{{ $servico->descricao }}</p>
                @endif
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $servicos->links('pagination::bootstrap-5') }}
    </div>
@endsection
