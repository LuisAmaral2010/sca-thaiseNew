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

    <div>
    <h2>Cadastrar Amostra</h2>

        @can('index-amostra')
            <a href="{{ route('amostras.index') }}">Listar</a><br><br>
        @endcan

        <x-alert />
        
        <form action="{{ route('amostras.store') }}" method="POST">
            @csrf
            @method('POST')

            <label>Descrição: </label>
            <input type="text" name="descricao" id="descricao" placeholder="Descrição da Amostra" value="{{ old('descricao') }}"
                required><br><br>

            <label>solicitacao_id: </label>
            <input type="text" name="solicitacao_id" id="solicitacao_id" placeholder="solicitacao_id" value="{{ old('solicitacao_id') }}"
                required><br><br>

            <label>Validade em dias: </label>
            <input type="text" name="validade_dias" id="validade_dias" placeholder="validade em dias" value="{{ old('validade_dias') }}"
                required><br><br>
            
            <label>Condicinamento e Armazenamento: </label>
            <input type="text" name="condicao_armazenamento" id="condicao_armazenamento" placeholder="Condicionamento e armazenamento da amostra" value="{{ old('condicao_armazenamento') }}"
                required><br><br>
            
            <label>Número CRA: </label>
            <input type="text" name="numero_cra" id="numero_cra" placeholder="Número do CRA" value="{{ old('numero_cra') }}"
                required><br><br>   
        
            <button type="submit">Cadastrar</button>
        </form>
    </div>

@endsection
