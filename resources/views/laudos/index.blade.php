@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Laudos</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
        @forelse ($laudos as $laudo)
            Id: {{ $laudo->laudo_id }}<br>
            Data de Emissão: {{ $laudo->data_emissao }}<br>
            Data Laudo Cra: {{ $laudo->data_laudo_cra }}<br>
            Data Laudo Lab: {{ $laudo->data_laudo_lab }}<br>
            Status: {{ $laudo->status_atual }}<br>
            Ordem Serviço: {{ $laudo->ordem_servico_id }}<br>
            Avaliador: {{ $laudo->avaliador_matricula }}<br>

            <a href="{{ route('laudos.show', ['historico' => $laudo->laudo_id]) }}">Visualizar</a><br>
            
            <a href="{{ route('laudos.edit', ['historico' => $laudo->laudo_id]) }}">Editar</a><br>

            <form action="{{ route('laudos.destroy', ['historico' => $laudo->laudo_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
        @empty
            Nenhum registro encontrado!
    @endforelse

    {{ $laudos->links() }}
      
        </ul>
@endsection

<!--
x laudo_id 
x data_emissao
x data_laudo_cra
x data_laudo_lab
x status_atual
ordem_servico_id 
avaliador_matricula

-->
