<x-layout title="Editar a Amostra">

    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Amostras</span></h3>
            </div>
        </div>
    </section>  

    <div>
    <h2>Editar Amostra</h2>

        
    <a href="{{ route('amostras.index') }}">Listar</a><br><br>
    <a href="{{ route('amostras.show', ['amostra' => $amostra->amostra_id]) }}">Visualizar</a><br><br>

    <x-alert />
        

        <form action="{{ route('amostras.update', ['amostra'=> $amostra->amostra_id]) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Descrição: </label>
            <input type="text" name="descricao" id="descricao" placeholder="Descrição da Amostra" value="{{ old('descricao',$amostra->descricao) }}"
                required><br><br>

            <label>solicitacao_id: </label>
            <input type="text" name="solicitacao_id" id="solicitacao_id" placeholder="solicitacao_id" value="{{ old('solicitacao_id',$amostra->solicitacao_id) }}"
                required><br><br>

            <label>Validade em dias: </label>
            <input type="text" name="validade_dias" id="validade_dias" placeholder="validade em dias" value="{{ old('validade_dias',$amostra->validade_dias) }}"
                required><br><br>
            
            <label>Condicinamento e Armazenamento: </label>
            <input type="text" name="condicao_armazenamento" id="condicao_armazenamento" placeholder="Condicionamento e armazenamento da amostra" value="{{ old('condicao_armazenamento',$amostra->condicao_armazenamento) }}"
                required><br><br>
            
            <label>Número CRA: </label>
            <input type="text" name="numero_cra" id="numero_cra" placeholder="Número do CRA" value="{{ old('numero_cra',$amostra->numero_cra) }}"
                required><br><br>   
        
            <button type="submit">Salvar</button>
        </form>
    </div>

</x-layout>

<!--
        'descricao',
        'solicitacao_id',
        'validade_dias',
        'condicao_armazenamento',
        'numero_cra',
-->