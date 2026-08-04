@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Usuários</h1>
        </div>
        <a href="{{ route('users.create') }}" class="sca-btn sca-btn--primary">Cadastrar</a>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($users as $user)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $user->id }}</span>
                </div>

                <h3 class="sca-card__title">{{ $user->name }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>E-mail</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                </dl>

                <div class="sca-card__actions">
                    <a href="{{ route('users.show', ['user' => $user->id]) }}" class="sca-link">Visualizar</a>
                    <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="sca-link">Editar</a>

                    <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST" class="sca-card__delete-form">
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
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
@endsection
