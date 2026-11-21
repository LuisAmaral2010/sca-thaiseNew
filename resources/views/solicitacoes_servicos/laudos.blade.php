@extends('layouts.app')

@section('title', 'Laudos da Solicitação')

@section('hide_navbar', '1')
@section('use_sidebar', '1')

@section('content')
    <div class="sca-page-header">
        <div>
            <h1 class="sca-page-header__title">Laudos da Solicitação {{ $solicitacao->numero_solicitacao_servico ?? '#' . $solicitacao->solicitacao_servico_id }}</h1>
            <p class="sca-page-header__subtitle">{{ $solicitacao->descricao }} — laudos aprovados pelo RespTec.</p>
        </div>
        <a href="{{ route('solicitacoes_servicos.index') }}" class="sca-btn sca-btn--outline">Voltar</a>
    </div>

    <div class="sca-panel">
        <table class="sca-table">
            <thead>
                <tr>
                    <th>Número do laudo</th>
                    <th>Ordem de serviço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laudos as $laudo)
                    <tr>
                        <td>{{ $laudo->numero_laudo }}</td>
                        <td><span class="sca-badge">#{{ $laudo->ordem_servico_id }}</span></td>
                        <td>
                            <a href="{{ route('solicitacoes_servicos.laudos.pdf', [$solicitacao, $laudo]) }}" class="sca-btn sca-btn--primary sca-btn--sm" target="_blank" rel="noopener">Visualizar Laudo</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
