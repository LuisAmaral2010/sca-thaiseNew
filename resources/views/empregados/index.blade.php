@extends('layouts.app')

@section('title', 'Empregados')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Empregados</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($empregados as $empregado)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $empregado->empregado_id }}</span>
                </div>

                <h3 class="sca-card__title">{{ $empregado->nome }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Matrícula</dt>
                        <dd>{{ $empregado->matricula }}</dd>
                    </div>
                    <div>
                        <dt>Login</dt>
                        <dd>{{ $empregado->login }}</dd>
                    </div>
                    <div>
                        <dt>E-mail</dt>
                        <dd>{{ $empregado->email }}</dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
                    <a href="{{ route('empregados.show', ['empregado' => $empregado->empregado_id]) }}" class="sca-link">Visualizar</a>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $empregados->links('pagination::bootstrap-5') }}
    </div>
@endsection
