@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Perfis Acessos</span></h3>
            </div>
        </div>
    </section> 
        <ul class="list-group">
            @forelse ($perfis_acessos as $perfil_acesso)
                Id: {{ $perfil_acesso->perfil_acesso_id }}<br>
                Data Permissão: {{ $perfil_acesso->data_permissao }}<br>
                Tipo Perfil: {{ $perfil_acesso->tipo_perfil }}<br>
                Usuario: {{ $perfil_acesso->usuario_matricula }}<br>


                
                <a href="{{ route('perfis_acessos.show', ['perfil_acesso' => $perfil_acesso->perfil_acesso_id]) }}">Visualizar</a>
            
                <a href="{{ route('perfis_acessos.edit', ['perfil_acesso' => $perfil_acesso->perfil_acesso_id]) }}">Editar</a>

                <form action="{{ route('perfis_acessos.destroy', ['perfil_acesso' => $perfil_acesso->perfil_acesso_id]) }}" method="POST">
                    @csrf
                    @method('delete')

                    <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
                </form>            
            @empty
                Nenhum registro encontrado!
            @endforelse

            {{ $perfis_acessos->links() }}

        </ul>
@endsection

<!--
x perfil_acesso_id 
x data_permissao
x tipo_perfil
x usuario_matricula
-->

