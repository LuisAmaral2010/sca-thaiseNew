@extends('layouts.app')

@section('title', 'Status de Usuário')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Status de Usuário</h1>
        </div>
        <a href="{{ route('user_statuses.create') }}" class="sca-btn sca-btn--primary">Cadastrar</a>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($userStatuses as $userStatus)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $userStatus->id }}</span>
                </div>

                <h3 class="sca-card__title">{{ $userStatus->name }}</h3>

                <div class="sca-card__actions">
                    <a href="{{ route('user_statuses.show', ['userStatus' => $userStatus->id]) }}" class="sca-link">Visualizar</a>
                    <a href="{{ route('user_statuses.edit', ['userStatus' => $userStatus->id]) }}" class="sca-link">Editar</a>

                    <form action="{{ route('user_statuses.destroy', ['userStatus' => $userStatus->id]) }}" method="POST" class="sca-card__delete-form">
                        @csrf
                        @method('delete')
                        <button type="submit" class="sca-link sca-link--danger" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $userStatuses->links('pagination::bootstrap-5') }}
    </div>
@endsection
