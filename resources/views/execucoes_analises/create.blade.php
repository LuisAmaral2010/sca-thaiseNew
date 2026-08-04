@extends('layouts.app')
 
@section('title', 'Page Title')

@section('content')

    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">
            <div class="section-title">
                <h3><span>Execuções Análises</span></h3>
            </div>
        </div>
    </section>  

    <div>
    <h2>Cadastrar Execuções Análises</h2>

        @can('index-execucoes_analises')
            <a href="{{ route('execucoes_analises.index') }}">Listar</a><br><br>
        @endcan

        <x-alert />
        
        <form action="{{ route('execucoes_analises.store') }}" method="POST">
            @csrf
            @method('POST')

            <label for="fracao_amostra_id">Fracao Amostra id:</label>
            <select name="fracao_amostra_id" id="fracao_amostra_id">
                <option value="">-- Selecione --</option>
                @foreach($fracoes_amostras as $fracao_amostra)
                    <option value="{{ $fracao_amostra->id }}" {{ old('fracao_amostra_id') == $fracao_amostra->id ? 'selected' : '' }}>
                        {{ $fracao_amostra->fracao_amostra_id }}
                    </option>
                @endforeach
            </select>
            <br><br>

            <label>Laudo Id: </label>
            <input type="text" name="laudo_id" id="laudo_id" placeholder="laudo_id" value="{{ old('laudo_id') }}">
            <br><br>

            <label for="ordem_servico_id">Ordem Servico:</label>
            <select name="ordem_servico_id" id="ordem_servico_id">
                <option value="">-- Selecione --</option>
                @foreach($ordens_servicos as $ordem_servico)
                    <option value="{{ $ordem_servico->id }}" {{ old('ordem_servico_id') == $ordem_servico->id ? 'selected' : '' }}>
                        {{ $ordem_servico->ordem_servico_id }}
                    </option>
                @endforeach
            </select>
            <br><br> 
                
            <label for="servico_id">Servico:</label>
            <select name="servico_id" id="servico_id" style="width:800px; height:40px;">
                <option value="">-- Selecione --</option>
                @foreach($servicos as $servico)
                    <option value="{{ $servico->id }}" {{ old('servico_id') == $servico->id ? 'selected' : '' }}>
                        {{ $servico->descricao }}
                    </option>
                @endforeach
            </select>
            <br><br> 

            <label>Is Concluido: </label>
            <input  type="checkbox" 
                    name="is_concluido"
                    id="is_concluido" 
                    value="1" {{ old('id="is_concluido"') ? 'checked' : '' }}>
            <br><br>

            <label>Data Conclusao: </label>
            <input  type="date" 
                    name="data_conclusao" 
                    id="data_conclusao" 
                    placeholder="Data da conclusao" 
                    value="{{ old('data_conclusao', 
                                isset($execucoes_analises) ? 
                                $execucoes_analises->data_conclusao->format('Y-m-d') : '') 
                                }}">
            <br><br>
                
            <label>Is Cancelado: </label>
            <input  type="checkbox" 
                    name="is_cancelado"
                    id="is_cancelado" 
                    value="1" {{ old('id="is_cancelado"') ? 'checked' : '' }}>
            <br><br>

            <label>Data Cancelamento: </label>
            <input type="date" 
                name="data_cancelamento" 
                id="data_cancelamento" 
                placeholder="Data do cancelamento" 
                value="{{   old('data_cancelamento',
                            isset($execucoes_analises) ?  
                            $execucoes_analises->data_cancelamento->format('Y-m-d') : '') 
                            }}">
            <br><br>

            <label>Observacao: </label>
            <input type="text" name="observacao" id="observacao" placeholder="observacao" value="{{ old('observacao') }}">
            <br><br>

            <button type="submit">Cadastrar</button>
        </form>
    </div>

@endsection

{{--
        'execucao_analise_id',
        'fracao_amostra_id',
        'laudo_id',
        'ordem_servico_id',
        'servico_id',
        'is_concluido',
        'data_conclusao',
        'is_cancelado',
        'data_cancelamento',

        'observacao',
        'created_at',
        'updated_at'
        */
        --}}