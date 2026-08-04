<x-layout title="Mostrar detalhes da Amostra">

    <section id="perfil" class="clients section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Amostras</span></h3>
            </div>
        </div>
    </section>  

    <div>

        <h2>Detalhes da Amostra</h2>

        <a href="{{ route('amostras.index') }}">Listar</a><br>
        <a href="{{ route('amostras.edit', ['amostra' => $amostra->amostra_id]) }}">Editar</a><br><br>

        <x-alert />

        {{-- Imprimir o registro --}}
        Descricao: {{ $amostra->descricao }}<br>
        Solicitacao Id: {{ $amostra->solicitacao_id }}<br>
        Descricao: {{ $amostra->validade_dias }}<br>
        Solicitacao Id: {{ $amostra->condicao_armazenamento }}<br>
        numero_cra: {{ $amostra->numero_cra }}<br>
        Cadastrado: {{ \Carbon\Carbon::parse($amostra->created_at)->format('d/m/Y H:i:s') }}<br>
        Editado: {{ \Carbon\Carbon::parse($amostra->updated_at)->format('d/m/Y H:i:s') }}<br>
    </div>
</x-layout>

<!--
        'descricao',
        'solicitacao_id',
        'validade_dias',
        'condicao_armazenamento',
        'numero_cra',
-->