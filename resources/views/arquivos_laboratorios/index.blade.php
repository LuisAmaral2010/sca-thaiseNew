@extends('layouts.app')

 
    @section('title', 'Page Title')

    @section('content')
        <section id="perfil" class="clients section-bg">
            <div class="container" data-aos="fade-up">

                <div class="section-title">
                    <h3><span>Arquivos dos Laboratórios</span></h3>
                </div>
            </div>
        </section>  
        <ul class="list-group">
        @forelse ($arquivos_laboratorios as $arquivo_laboratorio)
            Id: {{ $arquivos_laboratorios->arquivo_laboratorio_id }}<br>
            Tipo de Conteúdo: {{ $arquivos_laboratorios->content_type }}<br>
            Conteúdo do Documento: {{ $arquivos_laboratorios->document_content }}<br>
            Nome: {{ $arquivos_laboratorios->nome }}<br>
            Tamanho: {{ $arquivos_laboratorios->tamanho }}<br>
            Aprovado RespTec: {{ $arquivos_laboratorios->aprovado_resp_tec }}<br>
            Data Apreciação: {{ $arquivos_laboratorios->data_apreciacao }}<br>
            Observacao: {{ $arquivos_laboratorios->observacao }}<br>
            Laudo: {{ $arquivos_laboratorios->laudo_id }}<br>

            <a href="{{ route('arquivos_laboratorios.show', ['arquivo_laboratorio' => $arquivo_laboratorio->amostra_id]) }}">Visualizar</a><br>

            <a href="{{ route('arquivos_laboratorios.edit', ['arquivo_laboratorio' => $arquivo_laboratorio->amostra_id]) }}">Editar</a><br>

            <form action="{{ route('arquivos_laboratorios.destroy', ['arquivo_laboratorio' => $arquivo_laboratorio->amostra_id]) }}" method="POST">
                @csrf
                @method('delete')

                <button type="submit" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
            </form>
            <hr>
                @empty
                    Nenhum registro encontrado!
        @endforelse

        {{ $arquivos_laboratorios->links() }}
      
        </ul>
@endsection

