@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Empregados</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
        @forelse ($fracoes_amostras as $fracao_amostra)
            Id: {{ $fracao_amostra->empregado_id }}<br>
            Matricula: {{ $fracao_amostra->matricula }}<br>
            Nome: {{ $fracao_amostra->nome }}<br>
            login: {{ $fracao_amostra->login }}<br>
            email: {{ $fracao_amostra->email }}<br>

            <a href="{{ route('fracoes_amostras.show', ['fracao_amostra' => $fracao_amostra->empregado_id]) }}">Visualizar</a><br>
            
            <a href="{{ route('fracoes_amostras.edit', ['fracao_amostra' => $fracao_amostra->empregado_id]) }}">Editar</a><br>

            <form action="{{ route('fracoes_amostras.destroy', ['fracao_amostra' => $fracao_amostra->empregado_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <!-- <hr> -->
        @empty
            Nenhum registro encontrado!
        @endforelse

        {{ $fracoes_amostras->links() }}
      
    </ul>
<!-- </x-layout> -->
@endsection
