{{--
    Relatório de Análise gerado a partir do laudo online
    (LaboratorioCriarLaudoOnlineController::gerarPdf). Mesmo conteúdo do
    modelo storage/app/templates/Modelo_laudo.docx. Renderizado com dompdf:
    só CSS 2.1 básico, sem flexbox/grid.

    Os resultados ($amostra['analises'][]['html']) já vêm sanitizados por
    App\Support\ResultadoLaudoHtml e com as imagens embutidas em base64.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Análise {{ $numeroLaudo ?? '' }}</title>
    <style>
        @page {
            margin: 3.4cm 1.8cm 2.6cm 1.8cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #111;
            line-height: 1.35;
        }

        /* Cabeçalho e rodapé repetidos em todas as páginas */
        .cabecalho {
            position: fixed;
            top: -2.8cm;
            left: 0;
            right: 0;
            height: 2.4cm;
            border-bottom: 1px solid #000;
        }
        .cabecalho table {
            width: 100%;
            border-collapse: collapse;
        }
        .cabecalho td {
            vertical-align: middle;
            padding: 0;
        }
        .cabecalho .logo {
            height: 2cm;
        }
        .cabecalho .titulo {
            text-align: right;
            font-size: 13pt;
            font-weight: bold;
        }
        .cabecalho .numero {
            text-align: right;
            font-size: 10pt;
            margin-top: 18pt; /* espaço para "Página X de Y", desenhado pelo dompdf */
        }
        .rodape {
            position: fixed;
            bottom: -2.1cm;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
            padding-top: 4pt;
            font-size: 6.5pt;
            text-align: center;
            color: #333;
        }
        .rodape .revisao {
            text-align: right;
            margin-top: 2pt;
        }

        .marca-previa {
            border: 1px solid #b45309;
            color: #b45309;
            text-align: center;
            font-weight: bold;
            padding: 4pt;
            margin-bottom: 10pt;
        }

        h2.secao {
            font-size: 10pt;
            background: #e5e5e5;
            border: 1px solid #000;
            padding: 3pt 6pt;
            margin: 12pt 0 0 0;
        }

        table.dados {
            width: 100%;
            border-collapse: collapse;
        }
        table.dados th,
        table.dados td {
            border: 1px solid #000;
            padding: 3pt 6pt;
            text-align: left;
            vertical-align: top;
        }
        table.dados th {
            width: 30%;
            font-weight: bold;
            background: #f5f5f5;
        }

        table.amostra {
            margin-top: 8pt;
        }

        /* Cada análise da amostra: título + resultado em bloco livre */
        .analise {
            margin-top: 8pt;
        }
        .analise-titulo {
            border-bottom: 1px solid #999;
            padding-bottom: 2pt;
            margin-bottom: 4pt;
        }
        .rotulo-resultado {
            font-weight: bold;
            margin-bottom: 3pt;
        }

        /* Conteúdo rico do resultado (editor TipTap) */
        .resultado p { margin: 0 0 4pt 0; }
        .resultado h2 { font-size: 11pt; margin: 4pt 0; }
        .resultado h3, .resultado h4 { font-size: 10pt; margin: 4pt 0; }
        .resultado ul, .resultado ol { margin: 0 0 4pt 0; padding-left: 16pt; }
        .resultado blockquote { margin: 0 0 4pt 0; padding-left: 6pt; border-left: 2px solid #999; color: #444; }
        .resultado img { max-width: 100%; margin: 4pt 0; }
        .resultado table {
            width: 100%;
            border-collapse: collapse;
            margin: 4pt 0;
        }
        .resultado table th,
        .resultado table td {
            border: 1px solid #555;
            padding: 2pt 4pt;
            vertical-align: top;
        }
        .resultado table th {
            background: #eee;
            font-weight: bold;
            text-align: left;
        }
        .resultado table p { margin: 0; }

        .observacoes p {
            margin: 4pt 0;
            text-align: justify;
        }

        .assinatura {
            margin-top: 20pt;
            text-align: center;
            page-break-inside: avoid;
        }
        .assinatura .local-data {
            text-align: right;
            margin-bottom: 30pt;
        }
        .assinatura .linha {
            margin: 0 auto;
            width: 7cm;
            border-top: 1px solid #000;
            padding-top: 3pt;
        }
        .assinatura .dispensa {
            margin-top: 8pt;
            font-style: italic;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="cabecalho">
        <table>
            <tr>
                <td style="width: 45%;">
                    <img class="logo" src="{{ $logo }}" alt="Embrapa Agroindústria de Alimentos">
                </td>
                <td>
                    <div class="titulo">RELATÓRIO DE ANÁLISE</div>
                    <div class="numero">Número: {{ $numeroLaudo ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="rodape">
        Empresa Brasileira de Pesquisa Agropecuária – Ministério da Agricultura, Pecuária e Abastecimento<br>
        Av. das Américas, 29.501 – Guaratuba – 23020-470 – Rio de Janeiro, RJ – Telefone (0xx21) 3622-9600 – Fax (0xx21) 3622-9713<br>
        Homepage: https://www.embrapa.br/agroindustria-de-alimentos - correio eletrônico: ctaa.cra@embrapa.br
        <div class="revisao">SQ 021FA – Revisão 06 – 25/06/2018</div>
    </div>

    @if ($previa)
        <div class="marca-previa">PRÉ-VISUALIZAÇÃO — documento sem validade até o envio ao Responsável Técnico</div>
    @endif

    <h2 class="secao">DADOS DO SOLICITANTE</h2>
    <table class="dados">
        <tr><th>Solicitante:</th><td>{{ $ordem['solicitante'] ?? '' }}</td></tr>
        <tr><th>Plano de Ação/Atividade:</th><td>{{ $ordem['atividade'] ?? '' }}</td></tr>
        <tr><th>Nome do Material:</th><td>{{ $ordem['material'] ?? '' }}</td></tr>
        <tr><th>Número da Requisição:</th><td>{{ $ordem['numero_solicitacao_servico'] ?? '' }}</td></tr>
        <tr><th>Data de Entrada:</th><td>{{ $ordem['data_entrada'] ?? '' }}</td></tr>
        <tr><th>Data da Análise:</th><td>{{ $ordem['data_analise'] ?? '' }}</td></tr>
    </table>

    <h2 class="secao">RESULTADOS OBTIDOS</h2>
    @foreach ($amostras as $amostra)
        <table class="dados amostra">
            <tr><th>Identificação da Amostra:</th><td>{{ $amostra['descricao'] ?? '' }}</td></tr>
            <tr><th>Código de CRA:</th><td>{{ $amostra['numero_cra'] ?? '' }}</td></tr>
        </table>

        @foreach ($amostra['analises'] as $analise)
            <div class="analise">
                <div class="analise-titulo"><strong>Análise:</strong> {{ $analise['servico'] ?? '' }}</div>
                <div class="rotulo-resultado">Resultado:</div>
                {{-- Conteúdo livre (fora de tabela); HTML já sanitizado por ResultadoLaudoHtml::sanitizar --}}
                <div class="resultado">{!! $analise['html'] !!}</div>
            </div>
        @endforeach
    @endforeach

    <h2 class="secao">OBSERVAÇÕES</h2>
    <div class="observacoes">
        <p>Referência do método.</p>
        <p>O Relatório de Análise refere-se exclusivamente à amostra analisada, sendo o solicitante responsável pela coleta e amostragem do material.</p>
        <p>Este Relatório de Análise só pode ser reproduzido por completo e com autorização deste laboratório.</p>
        <p>O solicitante tem prazo de 30 (trinta) dias, a partir da data de emissão, para realizar análise crítica do Relatório de Análise e solicitar a devolução da amostra.</p>
    </div>

    <div class="assinatura">
        <div class="local-data">Rio de Janeiro, {{ $dataExtenso }}.</div>
        <div class="linha">
            {{ $respTec ?? '' }}<br>
            Responsável Técnico<br>
            {{ $ordem['unidade_operacional'] ?? '' }}
        </div>
        <div class="dispensa">“Dispensa assinatura quando consultado eletronicamente”</div>
    </div>
</body>
</html>
