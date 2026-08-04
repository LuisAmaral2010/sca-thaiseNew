@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nova Solicitação de Serviço</h1>

    {{-- ERROS GERAIS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Há erros no formulário:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('solicitacoes_servicos.store') }}" method="POST">
        @csrf

        {{-- DADOS DA SOLICITAÇÃO --}}
        <div class="card mb-3">
            <div class="card-header">Dados da Solicitação</div>
            <div class="card-body">
                {{-- Select de Atividade --}}
                    <div class="row">
                        <div class="col-sm-1">
                            <label class="form-label">Atividade</label>
                        </div>
                                    
                        <div class="col-sm-11">
                            <select name="atividade_id"
                                class="form-select @error('atividade_id') is-invalid @enderror"
                                required>
                                <option value="">Selecione...</option>
                                @foreach($atividades as $atividade_id => $titulo)
                                    <option value="{{ $atividade_id }}">{{ $titulo }}</option>
                                @endforeach
                            </select>
                            @error('atividade_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                </div>
                <br>
                <!-- <div class="mb-3"> -->
                <div class="row">
                    <div class="col-sm-1">
                        <label class="form-label">Descrição</label>
                    </div>
                    <div class="col-sm-11">
                        <textarea class="form-control" rows="5" name="descricao"
                            class="form-control @error('descricao') is-invalid @enderror"
                            value="{{ old('descricao') }}" required></textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- </div> -->

            </div>
        </div>

        {{-- PANEL / TABELA DE AMOSTRAS --}}
        <div class="card">
            <div class="card-header">
                Amostras
                <button type="button" class="btn btn-sm btn-primary float-end" id="btnAddAmostra">
                    Adicionar Amostra
                </button>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered" id="tabelaAmostras">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Validade (dias)</th>
                            <th>Condição Armazenamento</th>
                            <th style="width: 50px;">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $oldAmostras = old('amostras', []);
                        @endphp

                        @foreach($oldAmostras as $i => $amostra)
                            <tr>
                                <td>
                                    <input type="text" name="amostras[{{ $i }}][descricao]"
                                           class="form-control"
                                           value="{{ $amostra['descricao'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="amostras[{{ $i }}][validade_dias]"
                                           class="form-control"
                                           value="{{ $amostra['validade_dias'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="text" name="amostras[{{ $i }}][condicao_armazenamento]"
                                           class="form-control"
                                           value="{{ $amostra['condicao_armazenamento'] ?? '' }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-linha">&times;</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <small class="text-muted">
                    Você pode adicionar várias amostras. Linhas totalmente vazias serão ignoradas no salvamento.
                </small>
            </div>
        </div>

        <div class="mt-3">
            {{-- Botão para abrir modal de serviços --}}
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalServicos">
                Selecionar Serviços
            </button>
        </div>        
                
        {{-- MODAL SERVIÇOS --}}     
        <div class="modal fade" id="modalServicos" tabindex="-1" aria-labelledby="modalServicosLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalServicosLabel">Selecionar Serviços</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Select de unidade operacional --}}
                        <div class="mb-3">
                            <label class="form-label">Unidade Operacional</label>
                            <select id="selectUnidadeOperacional" class="form-select">
                                <option value="">Selecione...</option>
                                @foreach($unidadesOperacionais as $u)
                                    <option value="{{ $u->unidade_operacional_id }}">
                                        {{ $u->nome }} (ID: {{ $u->unidade_operacional_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                        {{-- Tabela de serviços da unidade atual --}}
                            <div class="col-md-7">
                                <h6>Serviços da Unidade Selecionada</h6>
                            </div>        
                            <div> <!-- </div> -->
                                <table class="table table-sm table-bordered" id="tabelaServicos">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>Descrição</th>
                                            <th>Tipo</th>
                                            <th style="width:60px;">Selecionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- preenchido via JS --}}
                                    </tbody>
                                </table>
                                <small class="text-muted">
                                    Marque os serviços desejados; eles serão enviados junto com a Solicitação.
                                </small>
                            </div>
                        </div>     
                    </div>
                </div>
            </div>   

        </div>
<!--    </form> -->
</div>

{{-- Inputs hidden fora do modal, dentro do <form> --}}

    <!-- <div id="servicosSelecionadosInputs"></div> -->
    {{-- Serviços selecionados (inputs hidden gerados em JS) --}}
        <div id="servicosSelecionadosInputs">
            <div class="card mt-3">
                <div class="card-header">Serviços Selecionados</div>
                <div class="card-body">
                    <ul id="listaServicosSelecionados" class="mb-0">
                        {{-- preenchido via JS --}}
                    </ul>
                </div>
            </div>  
        </div>    
<br>

<!---- -->
{{-- RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS --}}
{{-- RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS --}}
{{-- RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS --}}
<div class="card mt-4">
    <div class="card-header">
        Relação de Amostras x Unidades Operacionais / Serviços
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 35%;">Amostra</th>
                        <th style="width: 25%;">Unidade Operacional</th>
                        <th>Serviço</th>
                    </tr>
                </thead>
                <tbody id="resumoAmostrasServicosBody">
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Preencha as amostras e selecione serviços para visualizar o resumo.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">Salvar</button>
</div>
</form> <!-- fechar aqui -->

{{-- JS para adicionar/remover linhas e manter os índices corretos --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAdd = document.getElementById('btnAddAmostra');
    const tbody  = document.querySelector('#tabelaAmostras tbody');

    function reindexarLinhas() {
        const linhas = tbody.querySelectorAll('tr');
        linhas.forEach((tr, index) => {
            tr.querySelectorAll('input').forEach(input => {
                // name atual, ex: amostras[3][descricao]
                const antigoName = input.getAttribute('name');
                const novoName = antigoName.replace(/amostras\[\d+\]/, 'amostras[' + index + ']');
                input.setAttribute('name', novoName);
            });
        });
    }

    function adicionarLinha(v = {}) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="amostras[][descricao]"
                       class="form-control"
                       value="${v.descricao || ''}">
            </td>
            <td>
                <input type="number" name="amostras[][validade_dias]"
                       class="form-control"
                       value="${v.validade_dias || ''}">
            </td>
            <td>
                <input type="text" name="amostras[][condicao_armazenamento]"
                       class="form-control"
                       value="${v.condicao_armazenamento || ''}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-remove-linha">&times;</button>
            </td>
        `;
        tbody.appendChild(tr);
        reindexarLinhas();
    }

    btnAdd.addEventListener('click', function () {
        adicionarLinha();
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-linha')) {
            e.target.closest('tr').remove();
            reindexarLinhas();
        }
    });

    // Se não houver old('amostras'), adiciona ao menos 1 linha
    if (tbody.children.length === 0) {
        adicionarLinha();
    }
});


/*
*
* Serviços / Unidades Operacionais + Resumo
*/
document.addEventListener('DOMContentLoaded', function () {
    const selectUnidade = document.getElementById('selectUnidadeOperacional');
    const tabelaServicosBody = document.querySelector('#tabelaServicos tbody');
    const divServicosSelecionados = document.getElementById('servicosSelecionadosInputs'); // <-- usar este id
    const listaServicosSelecionados = document.getElementById('listaServicosSelecionados');
    const resumoBody = document.getElementById('resumoAmostrasServicosBody');
    const tabelaAmostras = document.getElementById('tabelaAmostras');

    // Guardar serviços já selecionados (ids)
    let servicosSelecionados = new Set();
    // servico_id -> { descricao, tipo_servico, unidade_id, unidade_nome }
    let cacheServicos = new Map();

    // Carrega serviços quando escolher unidade operacional
    selectUnidade.addEventListener('change', function () {
        const unidadeId = this.value;
        tabelaServicosBody.innerHTML = '';

        if (!unidadeId) {
            return;
        }

        // nome exibido no select (para mostrar no resumo)
        const unidadeNome = this.options[this.selectedIndex].text;

        fetch(`{{ url('unidades_operacionais') }}/${unidadeId}/servicos`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    tabelaServicosBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center">Nenhum serviço encontrado para esta unidade.</td>
                        </tr>
                    `;
                    return;
                }

                data.forEach(servico => {
                    const idStr = String(servico.servico_id);

                    cacheServicos.set(idStr, {
                        descricao: servico.descricao,
                        tipo_servico: servico.tipo_servico ?? '',
                        unidade_id: unidadeId,
                        unidade_nome: unidadeNome
                    });

                    const checked = servicosSelecionados.has(idStr) ? 'checked' : '';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${servico.servico_id}</td>
                        <td>${servico.descricao}</td>
                        <td>${servico.tipo_servico ?? ''}</td>
                        <td class="text-center">
                            <input type="checkbox"
                                   class="chk-servico"
                                   value="${servico.servico_id}"
                                   ${checked}>
                        </td>
                    `;
                    tabelaServicosBody.appendChild(tr);
                });
            })
            .catch(() => {
                tabelaServicosBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            Erro ao carregar serviços.
                        </td>
                    </tr>
                `;
            });
    });

    // Quando marcar/desmarcar serviço na tabela
    tabelaServicosBody.addEventListener('change', function (e) {
        if (e.target.classList.contains('chk-servico')) {
            const id = String(e.target.value);

            if (e.target.checked) {
                servicosSelecionados.add(id);
            } else {
                servicosSelecionados.delete(id);
            }

            atualizarInputsHidden();
            atualizarResumoVisual();
            atualizarResumoRelacao();
        }
    });

    // Cria / atualiza inputs hidden para enviar ao backend
    function atualizarInputsHidden() {
        divServicosSelecionados.innerHTML = '';
        Array.from(servicosSelecionados).forEach((id, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `servicos[${index}]`;
            input.value = id;
            divServicosSelecionados.appendChild(input);
        });
    }

    // Lista textual simples dos serviços selecionados
    function atualizarResumoVisual() {
        listaServicosSelecionados.innerHTML = '';

        if (servicosSelecionados.size === 0) {
            listaServicosSelecionados.innerHTML = '<li class="text-muted">Nenhum serviço selecionado.</li>';
            return;
        }

        Array.from(servicosSelecionados).forEach(id => {
            const dados = cacheServicos.get(id);
            const li = document.createElement('li');
            li.textContent = `ID ${id} - ${dados?.descricao ?? ''} (${dados?.tipo_servico ?? ''})`;
            listaServicosSelecionados.appendChild(li);
        });
    }

    // Coleta as amostras digitadas na tabela
    function coletarAmostras() {
        const amostras = [];

        if (!tabelaAmostras) {
            return amostras;
        }

        const linhas = tabelaAmostras.querySelectorAll('tbody tr');
        linhas.forEach(tr => {
            const desc = tr.querySelector('input[name*="[descricao]"]')?.value?.trim() || '';
            const validade = tr.querySelector('input[name*="[validade_dias]"]')?.value || '';
            const condicao = tr.querySelector('input[name*="[condicao_armazenamento]"]')?.value || '';

            // Ignora linha totalmente vazia
            if (desc === '' && validade === '' && condicao === '') {
                return;
            }

            amostras.push({
                descricao: desc,
                validade_dias: validade,
                condicao: condicao
            });
        });

        return amostras;
    }

    // Agrupa serviços selecionados por Unidade Operacional
    function agruparServicosPorUnidade() {
        const agrupado = {}; // chave: unidade_nome -> array de serviços

        Array.from(servicosSelecionados).forEach(id => {
            const dados = cacheServicos.get(id);
            if (!dados) return;

            const chave = dados.unidade_nome || `Unidade ${dados.unidade_id ?? ''}`;
            if (!agrupado[chave]) {
                agrupado[chave] = [];
            }

            agrupado[chave].push(dados);
        });

        return agrupado;
    }


    // Monta o resumo Amostra x Unidade / Serviços (3 colunas)
    /*
    function atualizarResumoRelacao() {
        if (!resumoBody) return;

        const amostras = coletarAmostras();
        const servicosPorUnidade = agruparServicosPorUnidade();

        resumoBody.innerHTML = '';

        if (amostras.length === 0) {
            resumoBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Nenhuma amostra informada.
                    </td>
                </tr>
            `;
            return;
        }

        if (Object.keys(servicosPorUnidade).length === 0) {
            resumoBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Nenhum serviço selecionado.
                    </td>
                </tr>
            `;
            return;
        }

        amostras.forEach(amostra => {
            const unidades = Object.keys(servicosPorUnidade);

            unidades.forEach((unidadeNome, idxUnidade) => {
                const servicos = servicosPorUnidade[unidadeNome];

                servicos.forEach((s, idxServico) => {
                    const tr = document.createElement('tr');

                    // Coluna Amostra (somente na primeira linha da primeira unidade/serviço)
                    const tdAmostra = document.createElement('td');
                    if (idxUnidade === 0 && idxServico === 0) {
                        tdAmostra.innerHTML = `
                            <strong>${amostra.descricao || '—'}</strong><br>
                            <small>
                                Validade: ${amostra.validade_dias || '—'} dias<br>
                                Condição: ${amostra.condicao || '—'}
                            </small>
                        `;
                        tdAmostra.rowSpan = unidades.reduce(
                            (total, un) => total + servicosPorUnidade[un].length,
                            0
                        );
                    } else {
                        tdAmostra.style.display = 'none';
                    }

                    // Coluna Unidade Operacional
                    const tdUnidade = document.createElement('td');
                    if (idxServico === 0) {
                        tdUnidade.textContent = unidadeNome;
                        tdUnidade.rowSpan = servicos.length;
                    } else {
                        tdUnidade.style.display = 'none';
                    }

                    // Coluna Serviço
                    const tdServico = document.createElement('td');
                    tdServico.textContent = s.descricao;
                    if (s.tipo_servico) {
                        const small = document.createElement('small');
                        small.classList.add('text-muted', 'ms-1');
                        small.textContent = `(${s.tipo_servico})`;
                        tdServico.appendChild(small);
                    }

                    tr.appendChild(tdAmostra);
                    tr.appendChild(tdUnidade);
                    tr.appendChild(tdServico);

                    resumoBody.appendChild(tr);
                });
            });
        });
    }
    */
       // Monta o resumo Amostra x Unidade / Serviços (3 colunas)
    function atualizarResumoRelacao() {
        if (!resumoBody) return;

        const amostras = coletarAmostras();
        const servicosPorUnidade = agruparServicosPorUnidade();

        resumoBody.innerHTML = '';

        if (amostras.length === 0) {
            resumoBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Nenhuma amostra informada.
                    </td>
                </tr>
            `;
            return;
        }

        if (Object.keys(servicosPorUnidade).length === 0) {
            resumoBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Nenhum serviço selecionado.
                    </td>
                </tr>
            `;
            return;
        }

        amostras.forEach(amostra => {
            const unidades = Object.keys(servicosPorUnidade);

            // total de linhas desta amostra = soma de todos os serviços de todas as unidades
            const totalLinhasAmostra = unidades.reduce(
                (total, un) => total + servicosPorUnidade[un].length,
                0
            );
            let linhaGlobal = 0; // conta as linhas desta amostra
            n = 1;
            unidades.forEach(unidadeNome => {
                
                const servicos = servicosPorUnidade[unidadeNome];
                servicos.forEach((s, idxServico) => {
                    const tr = document.createElement('tr');
                    linhaGlobal++;

                    // 1ª coluna: Amostra (apenas na primeira linha desta amostra)
                    if (linhaGlobal === 1) {
                        const tdAmostra = document.createElement('td');
                        tdAmostra.innerHTML = `
                            <strong>${amostra.descricao || '—'}</strong><br>
                            <small>
                                Validade: ${amostra.validade_dias || '—'} dias<br>
                                Condição: ${amostra.condicao || '—'}
                            </small>
                        `;
                        tdAmostra.rowSpan = totalLinhasAmostra;
                        tr.appendChild(tdAmostra);
                    }

                    // 2ª coluna: Unidade Operacional (mesclada por unidade)
                    
                    if (idxServico === 0) {
                        
                        const tdUnidade = document.createElement('td');
                        tdUnidade.textContent = "Fração " + n + ": " + unidadeNome;
                        tdUnidade.rowSpan = servicos.length;
                        tr.appendChild(tdUnidade);
                        
                    }

                    // 3ª coluna: Serviço
                    const tdServico = document.createElement('td');
                    tdServico.textContent = s.descricao;
                    if (s.tipo_servico) {
                        const small = document.createElement('small');
                        small.classList.add('text-muted', 'ms-1');
                        small.textContent = `(${s.tipo_servico})`;
                        tdServico.appendChild(small);
                    }
                    tr.appendChild(tdServico);

                    resumoBody.appendChild(tr);
                });
                n = n + 1;
            });
        });
    }


    // Atualiza o resumo quando o usuário editar amostras
    if (tabelaAmostras) {
        tabelaAmostras.addEventListener('input', atualizarResumoRelacao);
        tabelaAmostras.addEventListener('change', atualizarResumoRelacao);
    }

    // Chamada inicial
    atualizarResumoRelacao();
});



</script>
@endsection
