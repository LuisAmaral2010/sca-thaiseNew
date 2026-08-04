@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Históricos</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
        @forelse ($historicos as $historico)
            Id: {{ $historico->historico_id }}<br>
            Escopo: {{ $historico->escopo }}<br>
            Escopo Id: {{ $historico->escopo_id }}<br>
            Status: {{ $historico->status }}<br>
            Data: {{ $historico->data }}<br>
            Usuario: {{ $historico->usuario_matricula }}<br>


            <a href="{{ route('historicos.show', ['historico' => $historico->atividade_id]) }}">Visualizar</a><br>
            
            <a href="{{ route('historicos.edit', ['historico' => $historico->atividade_id]) }}">Editar</a><br>

            <form action="{{ route('historicos.destroy', ['historico' => $historico->atividade_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
        @empty
            Nenhum registro encontrado!
    @endforelse

    {{ $historicos->links() }}
      
        </ul>
@endsection

<!--
   x historico_id 
   x escopo
   x escopo_id
   x status
   x data
   x usuario_matricula
-->