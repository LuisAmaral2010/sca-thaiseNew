@extends('layouts.app')

@section('title', 'Permissões Unidades Operacionais')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Permissões Unidades Operacionais</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($permissoes_unidades_operacionais as $permissao_unidade_operacional)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $permissao_unidade_operacional->permissao_unidade_operacional_id }}</span>
                    <span class="sca-card__date">{{ $permissao_unidade_operacional->data_permissao }}</span>
                </div>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Unidade Operacional</dt>
                        <dd>{{ $permissao_unidade_operacional->unidade_operacional_id }}</dd>
                    </div>
                    <div>
                        <dt>Usuário</dt>
                        <dd>{{ $permissao_unidade_operacional->usuario_matricula }}</dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
                    <a href="{{ route('permissoes_unidades_operacionais.show', ['permissao_unidade_operacional' => $permissao_unidade_operacional->permissao_unidade_operacional_id]) }}" class="sca-link">Visualizar</a>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $permissoes_unidades_operacionais->links('pagination::bootstrap-5') }}
    </div>
@endsection
