@extends('layouts.app')

@section('title', 'Arquivos CRA')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Arquivos CRA</h1>
        </div>
    </div>

    <x-alert />

    <div class="sca-card-list">
        @forelse ($arquivos_cra as $arquivo_cra)
            <div class="sca-card">
                <div class="sca-card__header">
                    <span class="sca-badge">#{{ $arquivo_cra->arquivo_cra_id }}</span>
                    <span class="sca-card__date">{{ $arquivo_cra->data_apreciacao }}</span>
                </div>

                <h3 class="sca-card__title">{{ $arquivo_cra->nome }}</h3>

                <dl class="sca-card__meta">
                    <div>
                        <dt>Tipo de Conteúdo</dt>
                        <dd>{{ $arquivo_cra->content_type }}</dd>
                    </div>
                    <div>
                        <dt>Tamanho</dt>
                        <dd>{{ $arquivo_cra->tamanho }}</dd>
                    </div>
                    <div>
                        <dt>Aprovado Resp. Téc.</dt>
                        <dd>{{ $arquivo_cra->aprovado_resp_tec }}</dd>
                    </div>
                    <div>
                        <dt>Laudo</dt>
                        <dd>{{ $arquivo_cra->laudo_id }}</dd>
                    </div>
                </dl>

                @if ($arquivo_cra->observacao)
                    <p class="sca-card__description">{{ $arquivo_cra->observacao }}</p>
                @endif
            </div>
        @empty
            <div class="sca-empty">Nenhum registro encontrado!</div>
        @endforelse
    </div>

    <div class="sca-pagination">
        {{ $arquivos_cra->links('pagination::bootstrap-5') }}
    </div>
@endsection
