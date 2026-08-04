@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Execuções Analises</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($execucoes_analises as $execucao_analise)
                Id: {{ $execucao_analise->execucao_analise_id }}<br>
                Fração Amostra: {{ $execucao_analise->fracao_amostra_id }}<br>
                Laudo: {{ $execucao_analise->laudo_id }}<br>
                Ordem de Servico: {{ $execucao_analise->ordem_servico_id }}<br>
                Servico: {{ $execucao_analise->servico_id }}<br>
                Concluido: {{ $execucao_analise->is_concluido }}<br>
                Data Conclusão: {{ $execucao_analise->data_conclusao }}<br>
                Cancelado: {{ $execucao_analise->is_cancelado }}<br>
                Data Cancelamento: {{ $execucao_analise->data_cancelamento }}<br>
                Observacao: {{ $execucao_analise->observacao }}<br>

                Data Criação: {{ $execucao_analise->created_at }}<br>
                Data Atualização : {{ $execucao_analise->updated_at }}<br>
                <a href="{{-- route('execucoes_analises.show', ['execucao_analise' => $execucao_analise->execucao_analise_id]) --}}">Visualizar</a><br>
            
                <a href="{{-- route('execucoes_analises.edit', ['execucao_analise' => $execucao_analise->execucao_analise_id]) --}}">Editar</a><br>

                <form action="{{-- route('execucoes_analises.destroy', ['execucao_analise' => $execucao_analise->execucao_analise_id]) --}}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>            
                @empty
                    Nenhum registro encontrado!
            @endforelse

            {{ $execucoes_analises->links() }}

        </ul>
@endsection


