<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Empregados</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($empregados as $empregado)
            Id: {{ $empregado->empregado_id }}<br>
            Matricula: {{ $empregado->matricula }}<br>
            Nome: {{ $empregado->nome }}<br>
            login: {{ $empregado->login }}<br>
            email: {{ $empregado->email }}<br>

            <a href="{{ route('empregados.show', ['empregado' => $empregado->empregado_id]) }}">Visualizar</a><br>
            
            <a href="{{ route('empregados.edit', ['empregado' => $empregado->empregado_id]) }}">Editar</a><br>

            <form action="{{ route('empregados.destroy', ['empregado' => $empregado->empregado_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $empregados->links() }}

        </ul>
@endsection