@extends('layouts.app')

@section('title', 'Permissões Atividades')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Permissões Atividades</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($permissoes_atividades as $permissao_atividade)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $permissao_atividade->permissao_atividade_id }}</span>
                    <span class="sca-card__date">{{ $permissao_atividade->data_permissao }}</span>
                </div>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Atividade</dt>
                        <dd>{{ $permissao_atividade->atividade_id }}</dd>
                    </div>
                    <div>
                        <dt>Usuário</dt>
                        <dd>{{ $permissao_atividade->usuario_matricula }}</dd>
                    </div>
                    <div>
                        <dt>Permissão Resultado</dt>
                        <dd>{{ $permissao_atividade->permissao_resultado }}</dd>
                    </div>
                    <div>
                        <dt>Todos os Resultados</dt>
                        <dd>{{ $permissao_atividade->permissao_todos_resultados }}</dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
                    <a href="{{ route('permissoes_atividades.show', ['permissao_atividade' => $permissao_atividade->permissao_atividade_id]) }}" class="sca-link">Visualizar</a>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $permissoes_atividades->links('pagination::bootstrap-5') }}
    </div>
@endsection
