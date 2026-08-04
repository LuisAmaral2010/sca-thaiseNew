@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Arquivos CRA</span></h3>
            </div>
        </div>
    </section>  
    <ul class="list-group">
   @forelse ($arquivos_cra as $arquivo_cra)
        Id: {{ $arquivos_cra->arquivo_cra_id }}<br>
        Tipo de Conteúdo: {{ $arquivos_cra->content_type }}<br>
        Conteúdo do Documento: {{ $arquivos_cra->document_content }}<br>
        Nome: {{ $arquivos_cra->nome }}<br>
        Tamanho: {{ $arquivos_cra->tamanho }}<br>
        Aprovado RespTec: {{ $arquivos_cra->aprovado_resp_tec }}<br>
        Data Apreciação: {{ $arquivos_cra->data_apreciacao }}<br>
        Observacao: {{ $arquivos_cra->observacao }}<br>
        Laudo: {{ $arquivos_cra->laudo_id }}<br>


            <a href="{{ route('arquivos_cra.show', ['arquivo_cra' => $arquivo_cra->amostra_id]) }}">Visualizar</a><br>

            <a href="{{ route('arquivos_cra.edit', ['arquivo_cra' => $arquivo_cra->amostra_id]) }}">Editar</a><br>

            <form action="{{ route('arquivos_cra.destroy', ['arquivo_cra' => $arquivo_cra->amostra_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
    @empty
        Nenhum registro encontrado!
    @endforelse

    {{ $arquivos_cra->links() }}
      
        </ul>
@endsection