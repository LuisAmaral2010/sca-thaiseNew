@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Ordens Serviços</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($ordens_servicos as $ordem_servico)
                Id: {{ $ordem_servico->ordem_servico_id }}<br>
                Status: {{ $ordem_servico->status_atual }}<br>
                Data Status: {{ $ordem_servico->data_status_atual }}<br>
                Observacao: {{ $ordem_servico->observacao }}<br>
                Recebedor: {{ $ordem_servico->recebedor_matricula }}<br>
                Solicitacao Servico: {{ $ordem_servico->solicitacao_servico_id }}<br>
                Unidade Operacional: {{ $ordem_servico->unidade_operacional_id }}<br>

            <!--    <a href="{{-- route('ordens_acessos.show', ['ordem_servico' => $ordem_servico->empregado_id]) --}}">Visualizar</a><br> -->
            
            <!--    <a href="{{-- route('ordens_acessos.edit', ['ordem_servico' => $ordem_servico->empregado_id]) --}}">Editar</a><br> -->

                <form action="{{-- route('ordens_acessos.destroy', ['ordem_servico' => $ordem_servico->empregado_id]) --}}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $ordens_servicos->links() }}

        </ul>
@endsection

<!--
x ordem_servico_id
x status_atual
x data_status_atual
x observacao
x recebedor_matricula
x solicitacao_servico_id 
x unidade_operacional_id

-->
