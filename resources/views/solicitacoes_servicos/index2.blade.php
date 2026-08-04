@extends('layouts.app')

 
@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Listar Solicitações de Servicos</span></h3>
            </div>
                            <a href="{{ route('solicitacoes_servicos.create') }}">Cadastrar</a><br><br>
        </div>
    </section>  


@endsection