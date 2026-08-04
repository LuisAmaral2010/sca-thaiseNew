@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Solicitações de Serviço</h1>

    <table class="table table-striped">
        <thead>
            <tr><th></th>
                <th>ID</th>
                <th>Número CRA</th>
                <th>Data Solicitação</th>
                <th>Amostra</th>
                <th>Solicitante (Matrícula)</th>
                <th>Código</th>
                <th>Descrição Solicitação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td> <CRA type="button" class="btn btn-primary">CRA</button></td>
                    <td>{{ $item->solicitacao_servico_id }}</td>
                    <td>{{ $item->numero_cra }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->amostraDescricao }}</td>
                    <td>{{ $item->solicitante_matricula }}</td>
                    <td>{{ $item->codigo }}</td>
                    <td>{{ $item->solicitacaoDescricaolistasolicitacaoservico }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
