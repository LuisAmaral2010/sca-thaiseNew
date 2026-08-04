@extends('layouts.app')

@section('title', 'Atividades')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Atividades</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($atividades as $atividade)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $atividade->atividade_id }}</span>
                </div>

                <h3 class="sca-card__title">{{ $atividade->titulo }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Código</dt>
                        <dd>{{ $atividade->codigo }}</dd>
                    </div>
                    <div>
                        <dt>Plano de Ação</dt>
                        <dd>{{ $atividade->plano_acao_id }}</dd>
                    </div>
                    <div>
                        <dt>Período</dt>
                        <dd>{{ $atividade->data_inicio }} — {{ $atividade->data_fim }}</dd>
                    </div>
                    <div>
                        <dt>Responsável</dt>
                        <dd>{{ $atividade->matricula }}</dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd>{{ $atividade->status_atividade_descricao }} <span class="sca-card__muted">(#{{ $atividade->status_atividade_id }})</span></dd>
                    </div>
                </dl>

                @if ($atividade->descricao)
                    <p class="sca-card__description">{{ $atividade->descricao }}</p>
                @endif

                <div class="sca-card__actions">
                    <a href="{{ route('atividades.show', ['atividade' => $atividade->atividade_id]) }}" class="sca-link">Visualizar</a>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $atividades->links('pagination::bootstrap-5') }}
    </div>
@endsection
