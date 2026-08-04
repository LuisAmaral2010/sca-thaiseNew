@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Listar Solicitações de Servicos</span></h3>
            </div>
                            <a href="{{ route('solicitacoes_servicos.create') }}">Cadastrar</a><br><br>
        </div>
    </section>  
    <ul class="list-group">
   @forelse ($solicitacoes_servicos as $solicitacao_servico)
        Descrição: {{ $solicitacao_servico->descricao }}<br>
        Solicitacao Id: {{ $solicitacao_servico->solicitacao_servico_id }}<br> 
        <!-- Validade: {{-- $solicitacao_servico->data_solicitacao --}}<br> -->
        Validade: {{ \Carbon\Carbon::parse($solicitacao_servico->data_solicitacao)->format('d/m/Y') }}<br>
        Atividade: {{ $solicitacao_servico->atividade_id }}<br>
        {{-- Substitui atividade_id pelo título da atividade --}}
        Atividade:{{ $solicitacao_servico->atividade->titulo ?? '—' }}<br>
        Solicitante: {{ $solicitacao_servico->solicitante_matricula }}<br>
        Solicitante: {{ $solicitacao_servico->empregado->nome }}<br> 
        @can('index-solicitacao_servico')
            <a href="{{ route('solicitacao_servico.index', ['solicitacao_servico' => $solicitacao_servico->id]) }}">Turmas</a><br>
        @endcan

        @can('show-solicitacao_servico')
            <a href="{{ route('solicitacao_servicos.show', ['solicitacao_servico' => $solicitacao_servico->id]) }}">Visualizar</a><br>
        @endcan

        @can('edit-solicitacao_servico')
            <a href="{{ route('solicitacao_servicos.edit', ['solicitacao_servico' => $solicitacao_servico->id]) }}">Editar</a><br>
        @endcan

        @can('destroy-solicitacao_servico')
            <form action="{{ route('solicitacao_servicos.destroy', ['solicitacao_servico' => $solicitacao_servico->id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>

            </form>
        @endcan

        <hr>
    @empty
        Nenhum registro encontrado!
    @endforelse
 
        </ul>
    {{ $solicitacoes_servicos->links() }}
@endsection

