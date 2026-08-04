@extends('layouts.app')

@section('title', 'Perfis Acessos')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Perfis Acessos</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($perfis_acessos as $perfil_acesso)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $perfil_acesso->perfil_acesso_id }}</span>
                    <span class="sca-card__date">{{ $perfil_acesso->data_permissao }}</span>
                </div>

                <h3 class="sca-card__title">{{ $perfil_acesso->tipo_perfil }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Usuário</dt>
                        <dd>{{ $perfil_acesso->usuario_matricula }}</dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
                    <a href="{{ route('perfis_acessos.show', ['perfil_acesso' => $perfil_acesso->perfil_acesso_id]) }}" class="sca-link">Visualizar</a>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $perfis_acessos->links('pagination::bootstrap-5') }}
    </div>
@endsection
