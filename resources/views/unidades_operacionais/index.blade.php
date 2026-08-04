@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Unidades Operacionais</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($unidades_operacionais as $unidade_operacional)
                Id: {{ $unidade_operacional->unidade_operacional_id }}<br>
                Ativo: {{ $unidade_operacional->is_ativo }}<br>
                Nome: {{ $unidade_operacional->nome }}<br>
                Responsavel: {{ $unidade_operacional->responsavel_matricula }}<br>
                Responsavel Substituto: {{ $unidade_operacional->responsavel_substituto_matricula }}<br>

                <a href="{{ route('unidades_operacionais.show', ['unidade_operacional' => $unidade_operacional->unidade_operacional_id]) }}">Visualizar</a>
            
                <a href="{{ route('unidades_operacionais.edit', ['unidade_operacional' => $unidade_operacional->unidade_operacional_id]) }}">Editar</a>

                <form action="{{ route('unidades_operacionais.destroy', ['unidade_operacional' => $unidade_operacional->unidade_operacional_id]) }}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>     
                    <hr>       
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $unidades_operacionais->links() }}

        </ul>
@endsection

<!--
x unidade_operacional_id
x is_ativo
x nome
responsavel_matricula
responsavel_substituto_matricula


-->
