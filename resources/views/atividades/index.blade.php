@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Atividades</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
        @forelse ($atividades as $atividade)
            Id: {{ $atividade->atividade_id }}<br>
            Plano de Acao: {{ $atividade->plano_acao_id }}<br>
            Codigo: {{ $atividade->codigo }}<br>
            Título: {{ $atividade->titulo }}<br>
            Data Início: {{ $atividade->data_inicio }}<br>
            Data Fim: {{ $atividade->data_fim }}<br>
            Responsável: {{ $atividade->matricula }}<br>
            Descricao: {{ $atividade->descricao }}<br>
            Status Atividade Id: {{ $atividade->status_atividade_id }}<br>
            Status Atividade Descrição: {{ $atividade->status_atividade_descricao }}<br>



            <a href="{{ route('atividades.show', ['atividade' => $atividade->atividade_id]) }}">Visualizar</a><br>
            
            <a href="{{ route('atividades.edit', ['atividade' => $atividade->atividade_id]) }}">Editar</a><br>

            <form action="{{ route('atividades.destroy', ['atividade' => $atividade->atividade_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
        @empty
            Nenhum registro encontrado!
    @endforelse

    {{ $atividades->links() }}
      
        </ul>
@endsection
