@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Frações Amostras</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($fracoes_amostras as $fracao_amostra)
            Id: {{ $fracao_amostra->fracao_amostra_id }}<br>
            Status: {{ $fracao_amostra->status_atual }}<br>
            Data do Status: {{ $fracao_amostra->data_status_atual }}<br>
            Observacao: {{ $fracao_amostra->observacao }}<br>
            Amostra: {{ $fracao_amostra->amostra_id }}<br>
            Servico: {{ $fracao_amostra->servico_id }}<br>
            Ordem de Servico: {{ $fracao_amostra->ordem_servico_id }}<br>
            Responsavel: {{ $fracao_amostra->responsavel_execucao_matricula }}<br>

            <!-- <a href="{{-- route('fracoes_amostras.show', ['fracao_amostra' => $fracao_amostra->fracao_amostra_id]) --}}">Visualizar</a><br> -->
            
            <!-- <a href="{{-- route('fracoes_amostras.edit', ['fracao_amostra' => $fracao_amostra->fracao_amostra_id]) --}}">Editar</a><br> -->

            <!-- form action="{{-- route('fracoes_amostras.destroy', ['fracao_amostra' => $fracao_amostra->fracao_amostra_id]) --}}" method="POST"> -->
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </!-->            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $fracoes_amostras->links() }}

        </ul>
@endsection

<!--
x fracao_amostra_id
x status_atual
x data_status_atual
x observacao
x amostra_id Índice
x servico_id Índice
x ordem_servico_id Índice
responsavel_execucao_matricula
-->