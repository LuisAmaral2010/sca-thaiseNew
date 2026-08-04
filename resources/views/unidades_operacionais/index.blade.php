@extends('layouts.app')

@section('title', 'Unidades Operacionais')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Unidades Operacionais</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($unidades_operacionais as $unidade_operacional)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $unidade_operacional->unidade_operacional_id }}</span>
                </div>

                <h3 class="sca-card__title">{{ $unidade_operacional->nome }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Ativo</dt>
                        <dd>{{ $unidade_operacional->is_ativo ? 'Sim' : 'Não' }}</dd>
                    </div>
                    <div>
                        <dt>Responsável</dt>
                        <dd>{{ $unidade_operacional->responsavel_matricula }}</dd>
                    </div>
                    <div>
                        <dt>Responsável Substituto</dt>
                        <dd>{{ $unidade_operacional->responsavel_substituto_matricula }}</dd>
                    </div>
                </dl>

            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $unidades_operacionais->links('pagination::bootstrap-5') }}
    </div>
@endsection
