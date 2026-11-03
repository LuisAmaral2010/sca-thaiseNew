{{-- resources/views/solicitacoes_servicos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Solicitação de Serviço #' . $solicitacao->solicitacao_servico_id)

@section('hide_navbar', '1')
@section('use_sidebar', '1')

@section('content')
<div class="sca-page-header">
    <div>
        <h1 class="sca-page-header__title">
            Solicitação de Serviço {{ $solicitacao->numero_solicitacao_servico ?: '#' . $solicitacao->solicitacao_servico_id }}
        </h1>
    </div>
</div>

{{-- Mesmo layout do modal #modalResumo (create.blade.php), como página normal --}}
<div class="modal-content mb-4">

    <div class="modal-header">
        <h5 class="modal-title">
            RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS
        </h5>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <h6>Dados da Solicitação</h6>
            <div class="table-responsive">
            <table class="sca-table">
                <tbody>
                    <tr>
                        <th style="width: 180px;">Atividade</th>
                        <td>{{ $solicitacao->atividade->titulo ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Descrição</th>
                        <td>{!! nl2br(e($solicitacao->descricao ?? '—')) !!}</td>
                    </tr>
                    <tr>
                        <th>Solicitante</th>
                        <td>{{ $solicitacao->empregado->nome ?? $solicitacao->solicitante_matricula ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Data da Solicitação</th>
                        <td>{{ $solicitacao->data_solicitacao ? \Carbon\Carbon::parse($solicitacao->data_solicitacao)->format('d/m/Y') : '—' }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <div class="sca-panel">
            <div class="sca-panel__header">
                Relação de Amostras x Unidades Operacionais / Serviços
            </div>
            <div class="sca-panel__body">
                <div class="table-responsive">
                <table class="sca-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Amostra</th>
                            <th style="width: 24%;">Unidade Operacional</th>
                            <th style="width: 16%;">Status da Fração</th>
                            <th>Serviço</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resumo as $item)
                            @php $primeiraLinha = true; @endphp

                            @forelse($item['fracoes'] as $indiceFracao => $fracao)
                                @php $servicos = $fracao['servicos']; @endphp

                                @foreach($servicos->isEmpty() ? [null] : $servicos as $indiceServico => $servico)
                                    <tr>
                                        {{-- 1ª coluna: Amostra (apenas na primeira linha desta amostra) --}}
                                        @if($primeiraLinha)
                                            <td rowspan="{{ $item['total_linhas'] }}">
                                                <strong>{{ $item['amostra']->descricao ?: '—' }}</strong><br>
                                                <small>
                                                    Validade: {{ $item['amostra']->validade_dias ?: '—' }} dias<br>
                                                    Condição: {{ $item['amostra']->condicao_armazenamento ?: '—' }}
                                                </small>
                                            </td>
                                            @php $primeiraLinha = false; @endphp
                                        @endif

                                        {{-- 2ª coluna: Unidade Operacional (mesclada por fração) --}}
                                        @if($indiceServico === 0)
                                            <td rowspan="{{ max(1, $servicos->count()) }}">
                                                Fração {{ $indiceFracao + 1 }}: {{ $fracao['unidade'] }}
                                            </td>

                                            {{-- 3ª coluna: Status da fração (fracao_amostra.status_atual) --}}
                                            <td rowspan="{{ max(1, $servicos->count()) }}">
                                                @if($fracao['status'])
                                                    <span class="sca-badge">{{ str_replace('_', ' ', $fracao['status']) }}</span>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endif

                                        {{-- 4ª coluna: Serviço --}}
                                        <td>
                                            @if($servico)
                                                {{ $servico['descricao'] }}
                                                @if($servico['tipo'])
                                                    <small class="sca-card__muted" style="margin-left: 0.25rem;">({{ $servico['tipo'] }})</small>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td>
                                        <strong>{{ $item['amostra']->descricao ?: '—' }}</strong><br>
                                        <small>
                                            Validade: {{ $item['amostra']->validade_dias ?: '—' }} dias<br>
                                            Condição: {{ $item['amostra']->condicao_armazenamento ?: '—' }}
                                        </small>
                                    </td>
                                    <td colspan="3" class="sca-field__hint">Nenhum serviço selecionado.</td>
                                </tr>
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="4" class="text-center sca-field__hint">
                                    Nenhuma amostra informada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a href="{{ url()->previous() }}" class="sca-btn sca-btn--outline">Voltar</a>
    </div>

</div>

{{-- TRACE: histórico de status das ordens de serviço (ordem_servico_historico) --}}
<div class="sca-panel mb-4">
    <div class="sca-panel__header">
        Histórico das Ordens de Serviço
    </div>
    <div class="sca-panel__body">
        @if($tracos->isEmpty())
            <div class="sca-empty">Nenhum histórico registrado para as ordens de serviço desta solicitação.</div>
        @else
            <div class="trace-toolbar">
                <div class="trace-legend" id="traceLegend" aria-hidden="true"></div>
                <button type="button" class="sca-btn sca-btn--outline sca-btn--sm" id="traceAlternarTodos">
                    Expandir todos
                </button>
            </div>
            <p class="trace-dica">Clique em um ícone para ver o status, a data e a hora.</p>
            <div class="trace-chart" id="traceChart" role="group"
                 aria-label="Gráfico da evolução de status de cada ordem de serviço ao longo do tempo. Os mesmos dados estão na tabela abaixo.">
            </div>
            <div class="trace-icones" id="traceIcones" aria-hidden="true"></div>

            <details class="trace-table mt-3">
                <summary>Ver dados em tabela</summary>
                <div class="table-responsive">
                <table class="sca-table mb-0 mt-2">
                    <thead>
                        <tr>
                            <th>Ordem de Serviço</th>
                            <th>Unidade Operacional</th>
                            <th>Status</th>
                            <th>Data/hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tracos as $traco)
                            @foreach($traco['eventos'] as $evento)
                                <tr>
                                    <td>{{ $traco['ordem_servico_id'] }}</td>
                                    <td>{{ $traco['nome'] }}</td>
                                    <td>{{ $evento['status'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($evento['data'])->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                </div>
            </details>
        @endif
    </div>
</div>

@if($tracos->isNotEmpty())
<style>
    .trace-chart {
        --trace-ink: var(--sca-foreground);
        --trace-muted: var(--sca-muted-foreground);
        --trace-grid: var(--sca-border);
        --trace-surface: var(--sca-background);
        position: relative;
        width: 100%;
    }
    .trace-chart svg { display: block; width: 100%; overflow: visible; }
    .trace-chart text { font-family: var(--sca-font-sans); }
    .trace-chart .trace-icone { cursor: pointer; outline: none; }
    .trace-chart .trace-icone:focus-visible .trace-icone__foco { stroke: var(--trace-ink); }
    .trace-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem 1rem;
        margin-bottom: 0.25rem;
    }
    .trace-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem 1rem;
        font-size: 0.8125rem;
        color: var(--sca-foreground);
    }
    .trace-legend__item { display: inline-flex; align-items: center; gap: 0.375rem; }
    .trace-legend__swatch { width: 0.75rem; height: 0.75rem; border-radius: 3px; flex-shrink: 0; }
    .trace-dica { margin: 0 0 0.5rem; font-size: 0.75rem; color: var(--sca-muted-foreground); }
    .trace-icones {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem 1rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--sca-border);
        font-size: 0.75rem;
        color: var(--sca-muted-foreground);
    }
    .trace-icones__item { display: inline-flex; align-items: center; gap: 0.375rem; }
    .trace-table summary { cursor: pointer; font-size: 0.875rem; color: var(--sca-muted-foreground); }
</style>

<script>
(function () {
    const tracos = @json($tracos);

    // Paleta categórica, atribuída em ordem fixa (uma cor por ordem de serviço)
    const CORES = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#6250d6', '#e34948'];

    // Ícones (traços no estilo Lucide, viewBox 24x24) para cada status
    const ICONES = {
        mais: '<path d="M5 12h14"/><path d="M12 5v14"/>',
        enviar: '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/>',
        caixa: '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        ok: '<path d="M20 6 9 17l-5-5"/>',
        x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        proibido: '<circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/>',
        caminhao: '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        frasco: '<path d="M14 2v6a2 2 0 0 0 .245.96l5.51 10.08A2 2 0 0 1 18 22H6a2 2 0 0 1-1.755-2.96l5.51-10.08A2 2 0 0 0 10 8V2"/><path d="M6.453 15h11.094"/><path d="M8.5 2h7"/>',
        documento: '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>',
        documentoOk: '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/>',
        ponto: '<circle cx="12" cy="12" r="3"/>',
    };

    const ICONE_POR_STATUS = {
        'CRIADO': 'mais',
        'ENVIADO_CRA': 'enviar',
        'RECEBIDO_CRA': 'caixa',
        'ACEITO_CRA': 'ok',
        'REJEITADO_CRA': 'x',
        'REJEITADO_LABORATORIO': 'x',
        'CANCELADO_CRA': 'proibido',
        'CANCELADO_PELO_USUARIO': 'proibido',
        'ENVIADO_LABORATORIO': 'caminhao',
        'RECEBIDO PELO LABORATORIO': 'frasco',
        'PRÉVIA DO LAUDO CRIADA': 'documento',
        'LAUDO ENVIADO PARA RESPTEC': 'documentoOk',
        'LAUDO ESPERANDO APROVAÇÃO DO RESPTEC': 'documentoOk',
        'LAUDO APROVADO PELO RESPTEC': 'ok',
        'LAUDO REJEITADO PELO RESPTEC': 'x',
    };

    const iconeDe = status => ICONES[ICONE_POR_STATUS[status] || 'ponto'];

    const container = document.getElementById('traceChart');
    const legenda = document.getElementById('traceLegend');
    const legendaIcones = document.getElementById('traceIcones');
    const botaoTodos = document.getElementById('traceAlternarTodos');
    const NS = 'http://www.w3.org/2000/svg';

    const series = tracos.map((t, i) => ({
        ...t,
        cor: CORES[i % CORES.length],
        pontos: t.eventos.map(e => ({ status: e.status, data: new Date(e.data) })),
    }));

    // Pontos expandidos (chave "serie-ponto"); começa tudo recolhido
    const expandidos = new Set();
    const todasChaves = series.flatMap((s, i) => s.pontos.map((_, k) => `${i}-${k}`));

    const rotuloStatus = s => s.replace(/_/g, ' ');
    const pad2 = n => String(n).padStart(2, '0');
    const fmtData = d => `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;

    // Legenda das cores (sempre presente para 2+ séries)
    if (series.length > 1) {
        series.forEach(s => {
            const item = document.createElement('span');
            item.className = 'trace-legend__item';
            item.innerHTML = `<span class="trace-legend__swatch" style="background:${s.cor}"></span>`;
            item.append(`OS ${s.ordem_servico_id} · ${s.nome}`);
            legenda.appendChild(item);
        });
    }

    // Legenda dos ícones: só os status presentes, na ordem em que aparecem
    [...new Set(series.flatMap(s => s.pontos.map(p => p.status)))].forEach(status => {
        const item = document.createElement('span');
        item.className = 'trace-icones__item';
        item.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="var(--sca-muted-foreground)"/>`
            + `<g transform="translate(5 5) scale(0.5833)" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">${iconeDe(status)}</g></svg>`;
        item.append(rotuloStatus(status));
        legendaIcones.appendChild(item);
    });

    // Escala de tempo com marcas "redondas"
    const MIN = 60e3, HORA = 60 * MIN, DIA = 24 * HORA;
    const PASSOS = [MIN, 5 * MIN, 15 * MIN, 30 * MIN, HORA, 3 * HORA, 6 * HORA, 12 * HORA, DIA, 2 * DIA, 7 * DIA, 30 * DIA];

    function marcasTempo(ini, fim, alvo) {
        const passo = PASSOS.find(p => (fim - ini) / p <= alvo) || PASSOS[PASSOS.length - 1];
        // Alinha ao fuso local para que dias comecem à meia-noite
        const offset = new Date(ini).getTimezoneOffset() * MIN;
        const marcas = [];
        for (let t = Math.ceil((ini - offset) / passo) * passo + offset; t <= fim; t += passo) {
            marcas.push(t);
        }
        return { marcas, passo };
    }

    function fmtMarca(t, passo, anterior) {
        const d = new Date(t);
        const dia = `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}`;
        if (passo >= DIA) return dia;
        const hora = `${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
        const mudouDia = anterior === null || new Date(anterior).toDateString() !== d.toDateString();
        return mudouDia ? `${dia} ${hora}` : hora;
    }

    function el(nome, attrs, pai) {
        const n = document.createElementNS(NS, nome);
        Object.entries(attrs).forEach(([k, v]) => n.setAttribute(k, v));
        if (pai) pai.appendChild(n);
        return n;
    }

    // Quebra o texto em linhas curtas para caber na faixa da ordem
    function quebrarRotulo(texto, max) {
        const linhas = [];
        texto.split(' ').forEach(palavra => {
            const atual = linhas[linhas.length - 1];
            if (atual !== undefined && (atual + ' ' + palavra).length <= max) {
                linhas[linhas.length - 1] = atual + ' ' + palavra;
            } else {
                linhas.push(palavra);
            }
        });
        return linhas;
    }

    const RAIO = 12;          // raio do ícone
    const ESPACO_MIN = 30;    // distância mínima entre ícones recolhidos
    const LINHA_TEXTO = 15;

    function alternar(chave) {
        if (expandidos.has(chave)) {
            expandidos.delete(chave);
        } else {
            expandidos.add(chave);
        }
        desenhar(chave);
    }

    function atualizarBotaoTodos() {
        botaoTodos.textContent = expandidos.size === todasChaves.length ? 'Recolher todos' : 'Expandir todos';
    }

    botaoTodos.addEventListener('click', () => {
        if (expandidos.size === todasChaves.length) {
            expandidos.clear();
        } else {
            todasChaves.forEach(c => expandidos.add(c));
        }
        desenhar();
    });

    // Gráfico vertical: uma linha vertical por ordem de serviço, com o tempo
    // correndo de cima para baixo e um ícone em cada mudança de status.
    // Recolhido mostra só os ícones; clicar em um ícone abre ao lado o
    // status com data e hora.
    function desenhar(focarChave) {
        container.querySelector('svg')?.remove();

        const largura = container.clientWidth;
        const compacto = largura < 560;
        const fonte = compacto ? 10 : 11;

        const margem = { top: 40, right: 8, bottom: 16, left: compacto ? 64 : 92 };
        const plotW = Math.max(80, largura - margem.left - margem.right);
        const plotH = compacto ? 340 : 400;

        const tempos = series.flatMap(s => s.pontos.map(p => p.data.getTime()));
        let ini = Math.min(...tempos), fim = Math.max(...tempos);
        if (fim - ini < MIN) { ini -= 30 * MIN; fim += 30 * MIN; }
        const folga = (fim - ini) * 0.06;
        ini -= folga; fim += folga;

        // Uma faixa por ordem de serviço; a linha fica à esquerda da faixa e os detalhes à direita
        const faixa = plotW / series.length;
        const xLinha = i => margem.left + faixa * i + RAIO + 6;
        const y = t => margem.top + ((t - ini) / (fim - ini)) * plotH;
        const maxCaracteres = Math.max(8, Math.floor((faixa - RAIO * 2 - 40) / (fonte * 0.62)));

        // O SVG entra no DOM antes de desenhar, para medir os textos dos detalhes
        const svg = el('svg', { width: largura, height: margem.top + plotH + margem.bottom }, null);
        container.appendChild(svg);

        // Grade de tempo (horizontal) e rótulos no eixo Y
        const { marcas, passo } = marcasTempo(ini, fim, compacto ? 6 : 9);
        marcas.forEach((t, i) => {
            el('line', { x1: margem.left, x2: margem.left + plotW, y1: y(t), y2: y(t), stroke: 'var(--trace-grid)', 'stroke-width': 1 }, svg);
            const txt = el('text', { x: margem.left - 8, y: y(t) + 4, 'text-anchor': 'end', 'font-size': fonte, fill: 'var(--trace-muted)' }, svg);
            txt.textContent = fmtMarca(t, passo, i === 0 ? null : marcas[i - 1]);
        });

        let fundo = margem.top + plotH;

        series.forEach((s, i) => {
            const x = xLinha(i);

            // Cabeçalho da faixa: identifica a ordem de serviço
            el('circle', { cx: x, cy: 18, r: 5, fill: s.cor }, svg);
            const cab = el('text', { x: x + 12, y: 22, 'font-size': fonte + 1, 'font-weight': 600, fill: 'var(--trace-ink)' }, svg);
            cab.textContent = `OS ${s.ordem_servico_id} · ${s.sigla.length > 12 ? s.sigla.slice(0, 12) + '…' : s.sigla}`;

            // Posição de cada ícone: no horário do evento, empurrado para baixo
            // quando encostaria no ícone (ou no detalhe aberto) anterior
            const layout = [];
            let limite = -Infinity;
            s.pontos.forEach((p, k) => {
                const chave = `${i}-${k}`;
                const aberto = expandidos.has(chave);
                const linhas = aberto ? quebrarRotulo(rotuloStatus(p.status), maxCaracteres) : [];
                const alturaDetalhe = aberto ? (linhas.length + 1) * LINHA_TEXTO + 12 : 0;
                const yReal = y(p.data.getTime());
                const yIcone = Math.max(yReal, limite);
                layout.push({ p, chave, aberto, linhas, alturaDetalhe, yReal, yIcone });
                // Próximo ícone: abaixo deste e, se aberto, abaixo da caixa de detalhe
                limite = aberto
                    ? Math.max(yIcone + ESPACO_MIN, yIcone - RAIO + alturaDetalhe + 8 + RAIO)
                    : yIcone + ESPACO_MIN;
                fundo = Math.max(fundo, yIcone + Math.max(RAIO, alturaDetalhe - RAIO) + 4);
            });

            // Linha vertical do primeiro ao último ícone
            el('line', {
                x1: x, x2: x,
                y1: layout[0].yIcone, y2: layout[layout.length - 1].yIcone,
                stroke: s.cor, 'stroke-width': 2, 'stroke-linecap': 'round',
            }, svg);

            layout.forEach(({ p, chave, aberto, linhas, alturaDetalhe, yReal, yIcone }) => {
                // Marca o horário real quando o ícone precisou se afastar dele
                if (yIcone - yReal > 2) {
                    el('circle', { cx: x, cy: yReal, r: 2.5, fill: s.cor }, svg);
                }

                // Detalhe aberto: status, data e hora ao lado do ícone
                if (aberto) {
                    const caixa = el('rect', {
                        x: x + RAIO + 8, y: yIcone - RAIO, rx: 6,
                        height: alturaDetalhe, width: 10,
                        fill: 'var(--trace-surface)', stroke: s.cor, 'stroke-width': 1,
                    }, svg);
                    let larguraTexto = 0;
                    linhas.forEach((linha, j) => {
                        const t = el('text', {
                            x: x + RAIO + 16, y: yIcone - RAIO + 3 + (j + 1) * LINHA_TEXTO,
                            'font-size': fonte, 'font-weight': 600, fill: 'var(--trace-ink)',
                        }, svg);
                        t.textContent = linha;
                        larguraTexto = Math.max(larguraTexto, t.getComputedTextLength());
                    });
                    const tData = el('text', {
                        x: x + RAIO + 16, y: yIcone - RAIO + 3 + (linhas.length + 1) * LINHA_TEXTO,
                        'font-size': fonte, fill: 'var(--trace-muted)',
                    }, svg);
                    tData.textContent = fmtData(p.data);
                    larguraTexto = Math.max(larguraTexto, tData.getComputedTextLength());
                    caixa.setAttribute('width', Math.ceil(larguraTexto) + 16);
                }

                // Ícone clicável (também por teclado: Enter ou Espaço)
                const g = el('g', {
                    class: 'trace-icone',
                    tabindex: 0,
                    role: 'button',
                    'data-chave': chave,
                    'aria-expanded': String(aberto),
                    'aria-label': `OS ${s.ordem_servico_id}: ${rotuloStatus(p.status)}, ${fmtData(p.data)}`,
                }, svg);
                el('circle', { class: 'trace-icone__foco', cx: x, cy: yIcone, r: RAIO + 3, fill: 'none', stroke: aberto ? s.cor : 'transparent', 'stroke-width': 1.5 }, g);
                el('circle', { cx: x, cy: yIcone, r: RAIO, fill: s.cor, stroke: 'var(--trace-surface)', 'stroke-width': 2 }, g);
                const icone = el('g', {
                    transform: `translate(${x - 7} ${yIcone - 7}) scale(${14 / 24})`,
                    fill: 'none', stroke: '#fff', 'stroke-width': 2.5, 'stroke-linecap': 'round', 'stroke-linejoin': 'round',
                }, g);
                icone.innerHTML = iconeDe(p.status);
                el('title', {}, g).textContent = `${rotuloStatus(p.status)} · ${fmtData(p.data)}`;

                g.addEventListener('click', () => alternar(chave));
                g.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        alternar(chave);
                    }
                });
            });
        });

        const altura = Math.ceil(fundo + margem.bottom);
        svg.setAttribute('height', altura);
        svg.setAttribute('viewBox', `0 0 ${largura} ${altura}`);

        atualizarBotaoTodos();

        if (focarChave) {
            svg.querySelector(`[data-chave="${focarChave}"]`)?.focus();
        }
    }

    desenhar();

    let timer;
    window.addEventListener('resize', () => {
        clearTimeout(timer);
        timer = setTimeout(() => desenhar(), 100);
    });
})();
</script>
@endif
@endsection
