@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Permissões Atividades</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($permissoes_atividades as $permissao_atividade)
                Id: {{ $permissao_atividade->permissao_atividade_id }}<br>
                Data Permissão: {{ $permissao_atividade->data_permissao }}<br>
                Atividade: {{ $permissao_atividade->atividade_id }}<br>
                Usuario: {{ $permissao_atividade->usuario_matricula }}<br>
                Permissao Resultado: {{ $permissao_atividade->permissao_resultado }}<br>
                Permissao Todos Resultados: {{ $permissao_atividade->permissao_todos_resultados }}<br>
                
                <a href="{{ route('permissoes_atividades.show', ['permissao_atividade' => $permissao_atividade->permissao_atividade_id]) }}">Visualizar</a>
            
                <a href="{{ route('permissoes_atividades.edit', ['permissao_atividade' => $permissao_atividade->permissao_atividade_id]) }}">Editar</a>

                <form action="{{ route('permissoes_atividades.destroy', ['permissao_atividade' => $permissao_atividade->permissao_atividade_id]) }}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $permissoes_atividades->links() }}

        </ul>
@endsection

<!--
x permissao_atividade_id 
x data_permissao
x atividade_id
x usuario_matricula
permissao_resultado
permissao_todos_resultados
created_at
updated_at

-->


