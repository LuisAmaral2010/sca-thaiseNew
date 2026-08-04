@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Listar Serviços</span></h3>

                <a href="{{ route('servicos.create') }}">Cadastrar</a><br><br>
            </div>
        </div>
    </section>  
    <ul class="list-group">
        @forelse ($servicos as $servico)
            Id: {{ $servico->laudo_id }}<br>
            Ativo: {{ $servico->is_ativo }}<br>
            Data Cadastro: {{ $servico->data_cadastro }}<br>
            Descricao: {{ $servico->descricao }}<br>
            Unidade Operacional: {{ $servico->unidade_operacional_id }}<br>
            Tipo Serviço: {{ $servico->tipo_servico }}<br>

            <a href="{{ route('servicos.show', ['servico' => $servico->servico_id]) }}">Visualizar</a>
            
            <a href="{{ route('servicos.edit', ['servico' => $servico->servico_id]) }}">Editar</a>

            <form action="{{ route('servicos.destroy', ['servico' => $servico->servico_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
        @empty
            Nenhum registro encontrado!
    @endforelse

    {{ $servicos->links() }}
      
        </ul>
@endsection

<!--
x servico_id 
x is_ativo
x data_cadastro
x descricao
x unidade_operacional_id
tipo_servico
-->

