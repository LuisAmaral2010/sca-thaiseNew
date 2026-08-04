@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Permissões Unidades Operacionais</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($permissoes_unidades_operacionais as $permissao_unidade_operacional)
                Id: {{ $permissao_unidade_operacional->permissao_unidade_operacional_id }}<br>
                Data Permissão: {{ $permissao_unidade_operacional->data_permissao }}<br>
                Unidade Operacional: {{ $permissao_unidade_operacional->unidade_operacional_id }}<br>
                Usuario: {{ $permissao_unidade_operacional->usuario_matricula }}<br>
                
                <a href="{{ route('permissoes_unidades_operacionais.show', ['permissao_unidade_operacional' => $permissao_unidade_operacional->permissao_unidade_operacional_id]) }}">Visualizar</a>
            
                <a href="{{ route('permissoes_unidades_operacionais.edit', ['permissao_unidade_operacional' => $permissao_unidade_operacional->permissao_unidade_operacional_id]) }}">Editar</a>

                <form action="{{ route('permissoes_unidades_operacionais.destroy', ['permissao_unidade_operacional' => $permissao_unidade_operacional->permissao_unidade_operacional_id]) }}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $permissoes_unidades_operacionais->links() }}

        </ul>
@endsection

<!--
x permissao_unidade_atendimento_id 
x data_permissao
x unidade_operacional_id
x usuario_matricula


-->



