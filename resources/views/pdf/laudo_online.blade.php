{{--
    Relatório de Análise gerado a partir do laudo online
    (LaboratorioCriarLaudoOnlineController::gerarPdf). Reproduz o layout do
    modelo storage/app/templates/Modelo_laudo.docx: mesmas margens, cabeçalho
    em caixa, linhas de seção, tabela de dados com divisórias cinza, marca
    d'água, observações com marcadores ❖, assinatura e rodapé.

    Renderizado com dompdf: só CSS 2.1 básico, sem flexbox/grid. Medidas em
    pt, tiradas do .docx (1 pt = 20 twips):
      - página A4; margens esquerda/direita 1,5 cm (42.5pt)
      - cabeçalho a 36pt do topo; corpo começa em 84pt
      - rodapé com linha em 783pt

    Fontes (resources/fonts, licença OFL): Carlito tem as mesmas métricas da
    Calibri e Arimo as da Arial, usadas no modelo.

    Os resultados ($amostra['analises'][]['html']) já vêm sanitizados por
    App\Support\ResultadoLaudoHtml e com as imagens embutidas em base64.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Análise {{ $numeroLaudo ?? '' }}</title>
    <style>
        @font-face { font-family: 'Carlito'; font-weight: normal; font-style: normal; src: url('{{ $fontes }}/Carlito-Regular.ttf') format('truetype'); }
        @font-face { font-family: 'Carlito'; font-weight: bold; font-style: normal; src: url('{{ $fontes }}/Carlito-Bold.ttf') format('truetype'); }
        @font-face { font-family: 'Carlito'; font-weight: normal; font-style: italic; src: url('{{ $fontes }}/Carlito-Italic.ttf') format('truetype'); }
        @font-face { font-family: 'Carlito'; font-weight: bold; font-style: italic; src: url('{{ $fontes }}/Carlito-BoldItalic.ttf') format('truetype'); }
        @font-face { font-family: 'Arimo'; font-weight: normal; font-style: normal; src: url('{{ $fontes }}/Arimo-Regular.ttf') format('truetype'); }
        @font-face { font-family: 'Arimo'; font-weight: bold; font-style: normal; src: url('{{ $fontes }}/Arimo-Bold.ttf') format('truetype'); }
        @font-face { font-family: 'Arimo'; font-weight: normal; font-style: italic; src: url('{{ $fontes }}/Arimo-Italic.ttf') format('truetype'); }
        @font-face { font-family: 'Arimo'; font-weight: bold; font-style: italic; src: url('{{ $fontes }}/Arimo-BoldItalic.ttf') format('truetype'); }

        @page {
            margin: 84pt 42.5pt 64pt 42.5pt;
        }

        body {
            margin: 0;
            font-family: 'Carlito', 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            /* Espaçamento 1,5 linha do modelo (16,5pt). No dompdf, line-height
               em pt ou sem unidade sai 4/3 maior; em px sai exatamente em pt. */
            line-height: 16.5px;
            color: #000;
        }

        /* Marca d'água (logo Embrapa clareado e girado), a página inteira */
        .marca-dagua {
            position: fixed;
            top: -84pt;
            left: -42.5pt;
            width: 595.35pt;
            height: 842pt;
            z-index: -1; /* atrás do conteúdo */
        }
        .marca-dagua img {
            width: 595.35pt;
            height: 842pt;
        }

        /* Cabeçalho: caixa com logo | título | página e número do laudo */
        .cabecalho {
            position: fixed;
            top: -48pt;
            left: 0;
            right: 0;
        }
        .cabecalho table {
            border-collapse: collapse;
        }
        .cabecalho td {
            border: 0.5pt solid #000;
            padding: 0 1.5pt;
            line-height: 10px;
        }
        .cabecalho .logo-celula {
            width: 117.5pt; /* 120,5pt menos recuo e borda */
            text-align: center;
            vertical-align: middle;
        }
        .cabecalho .logo-celula img {
            width: 90pt;
            height: 33.75pt;
        }
        .cabecalho .titulo {
            width: 301.75pt;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 12pt;
        }
        .cabecalho .lateral {
            width: 82pt;
            height: 21pt;
            vertical-align: top;
            font-size: 8pt;
            line-height: 9.5px;
        }
        .cabecalho .numero {
            font-family: 'Arimo', sans-serif;
            font-size: 10pt;
            line-height: 11.5px;
        }

        .marca-previa {
            position: fixed;
            top: -70pt;
            right: 0;
            font-family: 'Arimo', sans-serif;
            font-size: 7.5pt;
            font-weight: bold;
            color: #b45309;
            line-height: 9px;
        }

        /* Rodapé: linha, endereço em itálico centralizado e código do formulário */
        .rodape {
            position: fixed;
            bottom: -37pt;
            left: 0;
            right: 0;
            height: 32pt;
            border-top: 0.75pt solid #000;
            font-size: 6pt;
            line-height: 7px;
        }
        .rodape .endereco {
            padding-top: 2pt;
            text-align: center;
            font-style: italic;
        }

        /* Títulos de seção: negrito, sublinhados por uma linha de 1,5pt */
        .secao {
            margin: 0;
            font-size: 9pt;
            font-weight: bold;
            border-bottom: 1.5pt solid #000;
            padding-bottom: 1pt;
        }
        .linha-vazia {
            height: 16.5pt;
        }

        /* Tabelas de dados: rótulo (Calibri 9) e valor (Arial 10) */
        table.dados {
            border-collapse: collapse;
        }
        table.dados td {
            padding: 0 5.4pt;
            vertical-align: top;
            line-height: 16.5px;
        }
        table.dados td.rotulo {
            width: 119.95pt; /* 130,75pt da coluna menos o recuo */
        }
        table.dados td.valor {
            width: 368.7pt; /* 379,5pt da coluna menos o recuo */
            font-family: 'Arimo', sans-serif;
            font-size: 10pt;
        }
        /* Só divisórias internas, cinza claro (D9D9D9), como no modelo */
        table.solicitante td {
            border-bottom: 0.5pt solid #d9d9d9;
        }
        table.solicitante tr.ultima td {
            border-bottom: none;
        }
        table.solicitante td.rotulo {
            border-right: 0.5pt solid #d9d9d9;
        }

        /* Bloco de cada amostra em RESULTADOS OBTIDOS */
        .amostra {
            padding-top: 16pt; /* parágrafo vazio antes da tabela no modelo */
        }

        /* "Resultado:" fora de tabela para o conteúdo rico poder quebrar
           entre páginas. O resultado vem na linha de baixo do rótulo,
           alinhado com ele (mesmo recuo de 5,4pt das células), sem indentação */
        .linha-resultado {
            padding-left: 5.4pt;
            padding-right: 5.4pt;
        }
        .linha-resultado .rotulo {
            line-height: 16.5px;
        }
        .linha-resultado .resultado {
            padding-top: 0;
            font-family: 'Arimo', sans-serif;
            font-size: 10pt;
            line-height: 13px;
        }

        /* Conteúdo rico do resultado (editor TipTap) */
        .resultado p { margin: 0 0 3pt 0; }
        .resultado h2 { font-size: 11pt; margin: 3pt 0; }
        .resultado h3, .resultado h4 { font-size: 10pt; margin: 3pt 0; }
        .resultado ul, .resultado ol { margin: 0 0 3pt 0; padding-left: 14pt; }
        .resultado blockquote { margin: 0 0 3pt 0; padding-left: 6pt; border-left: 1.5pt solid #999; color: #444; }
        .resultado img { max-width: 100%; margin: 3pt 0; }
        .resultado table {
            width: 100%;
            border-collapse: collapse;
            margin: 3pt 0;
            font-size: 9pt;
        }
        .resultado table th,
        .resultado table td {
            border: 0.5pt solid #555;
            padding: 1.5pt 3pt;
            vertical-align: top;
        }
        .resultado table th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .resultado table p { margin: 0; }

        .fim-resultados {
            height: 11pt; /* parágrafo vazio (9pt, simples) após os blocos */
        }

        /* Observações: marcador ❖, recuo deslocado de 17,85pt, justificado */
        .observacoes {
            font-family: 'Arimo', sans-serif;
            font-size: 10pt;
            line-height: 19px;
            text-align: justify;
        }
        .observacoes .item {
            position: relative;
            padding-left: 17.85pt;
        }
        .observacoes .marcador {
            position: absolute;
            left: 0;
            top: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
        }
        .linha-fim-observacoes {
            height: 16.5pt;
            border-bottom: 1.5pt solid #000;
        }

        .local-data {
            text-align: right;
        }

        /* Assinatura: tabela recuada 173pt, 228,65pt de largura, centralizada */
        .assinatura {
            margin-left: 173.1pt;
            width: 228.65pt;
            text-align: center;
            page-break-inside: avoid;
        }
        .assinatura .traco {
            height: 19.5pt;
            line-height: 19.5px;
        }
        .assinatura .dados-assinatura {
            line-height: 11.5px;
        }
    </style>
</head>
<body>
    <div class="marca-dagua"><img src="{{ $marcaDagua }}" alt=""></div>

    <div class="cabecalho">
        <table>
            <tr>
                <td class="logo-celula" rowspan="2">
                    <img src="{{ $logo }}" alt="Embrapa Agroindústria de Alimentos">
                </td>
                <td class="titulo" rowspan="2">RELATÓRIO DE ANÁLISE</td>
                {{-- "Página X de Y" é desenhado pelo dompdf nesta célula (gerarPdf) --}}
                <td class="lateral">&nbsp;</td>
            </tr>
            <tr>
                <td class="lateral">
                    Número:<br>
                    <span class="numero">{{ $numeroLaudo ?? '' }}</span>
                </td>
            </tr>
        </table>
    </div>

    @if ($previa)
        <div class="marca-previa">PRÉ-VISUALIZAÇÃO — sem validade até o envio ao Responsável Técnico</div>
    @endif

    <div class="rodape">
        <div class="endereco">
            Empresa Brasileira de Pesquisa Agropecuária – Ministério da Agricultura, Pecuária e Abastecimento<br>
            Av. das Américas, 29.501 – Guaratuba – 23020-470 – Rio de Janeiro, RJ – Telefone (0xx21) 3622-9600 – Fax (0xx21) 3622-9713<br>
            Homepage: https://www.embrapa.br/agroindustria-de-alimentos - correio eletrônico: ctaa.cra@embrapa.br
        </div>
        <div>SQ 021FA – Revisão 06 – 25/06/2018</div>
    </div>

    <div class="secao">DADOS DO SOLICITANTE</div>
    <div class="linha-vazia"></div>
    <table class="dados solicitante">
        <tr><td class="rotulo">Solicitante:</td><td class="valor">{{ $ordem['solicitante'] ?? '' }}</td></tr>
        <tr><td class="rotulo">Plano de Ação/Atividade:</td><td class="valor">{{ $ordem['atividade'] ?? '' }}</td></tr>
        <tr><td class="rotulo">Nome do Material:</td><td class="valor">{{ $ordem['material'] ?? '' }}</td></tr>
        <tr><td class="rotulo">Número da Requisição:</td><td class="valor">{{ $ordem['numero_solicitacao_servico'] ?? '' }}</td></tr>
        <tr><td class="rotulo">Data de Entrada:</td><td class="valor">{{ $ordem['data_entrada'] ?? '' }}</td></tr>
        <tr class="ultima"><td class="rotulo">Data da Análise:</td><td class="valor">{{ $ordem['data_analise'] ?? '' }}</td></tr>
    </table>
    <div class="linha-vazia"></div>

    <div class="secao">RESULTADOS OBTIDOS</div>
    @foreach ($amostras as $amostra)
        <div class="amostra">
            <table class="dados">
                <tr><td class="rotulo">Identificação da Amostra:</td><td class="valor">{{ $amostra['descricao'] ?? '' }}</td></tr>
                <tr><td class="rotulo">Código de CRA:</td><td class="valor">{{ $amostra['numero_cra'] ?? '' }}</td></tr>
            </table>

            @foreach ($amostra['analises'] as $analise)
                <table class="dados">
                    <tr><td class="rotulo">Análise:</td><td class="valor">{{ $analise['servico'] ?? '' }}</td></tr>
                </table>
                @if ($discriminar ?? true)
                    <div class="linha-resultado">
                        <div class="rotulo">Resultado:</div>
                        {{-- HTML já sanitizado por ResultadoLaudoHtml::sanitizar --}}
                        <div class="resultado">{!! $analise['html'] !!}</div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
    @unless ($discriminar ?? true)
        {{-- "Discriminar Fração Amostra" desligado: um único resultado para todas as amostras --}}
        <div class="amostra">
            <div class="linha-resultado">
                <div class="rotulo">Resultado:</div>
                <div class="resultado">{!! $resultadoUnico !!}</div>
            </div>
        </div>
    @endunless
    <div class="fim-resultados"></div>

    <div class="secao">OBSERVAÇÕES</div>
    <div class="linha-vazia"></div>
    <div class="observacoes">
        <div class="item"><span class="marcador">❖</span>Referência do método.</div>
        <div class="item"><span class="marcador">❖</span>O Relatório de Análise refere-se exclusivamente à amostra analisada, sendo o solicitante responsável pela coleta e amostragem do material.</div>
        <div class="item"><span class="marcador">❖</span>Este Relatório de Análise só pode ser reproduzido por completo e com autorização deste laboratório.</div>
        <div class="item"><span class="marcador">❖</span>O solicitante tem prazo de 30 (trinta) dias, a partir da data de emissão, para realizar <strong>análise crítica</strong> do Relatório de Análise e solicitar a devolução da amostra.</div>
    </div>
    <div class="linha-fim-observacoes"></div>
    <div class="linha-vazia"></div>

    <div class="local-data">Rio de Janeiro, {{ $dataExtenso }}.</div>
    <div class="linha-vazia"></div>

    <div class="assinatura">
        <div class="traco">________________________________</div>
        <div class="dados-assinatura">
            {{ $respTec ?? '' }}<br>
            Responsável Técnico<br>
            {{ $ordem['unidade_operacional'] ?? '' }}<br>
            “Dispensa assinatura quando consultado eletronicamente”
        </div>
    </div>
</body>
</html>
