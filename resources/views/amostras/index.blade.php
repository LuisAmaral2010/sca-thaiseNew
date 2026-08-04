@extends('layouts.app')
 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Amostras</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
   @forelse ($amostras as $amostra)
        Id: {{ $amostra->amostra_id }}<br>
        Descrição: {{ $amostra->descricao }}<br>
        Solicitacao Id: {{ $amostra->solicitacao_id }}<br>
        Validade: {{ $amostra->validade_dias }}<br>
        Armazenamento: {{ $amostra->condicao_armazenamento }}<br>
        CRA: {{ $amostra->numero_cra }}<br>

            <a href="{{ route('amostras.show', ['amostra' => $amostra->amostra_id]) }}">Visualizar</a><br>

            <a href="{{ route('amostras.edit', ['amostra' => $amostra->amostra_id]) }}">Editar</a><br>

            <form action="{{ route('amostras.destroy', ['amostra' => $amostra->amostra_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
    @empty
        Nenhum registro encontrado!
    @endforelse
    </ul>
    {{ $amostras->links() }}
      
@endsection
