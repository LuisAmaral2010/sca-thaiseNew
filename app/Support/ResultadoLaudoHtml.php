<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

// Tratamento do conteúdo rico (HTML) dos resultados de análise digitados na
// tela "Criar Laudo Online": textos formatados, listas, tabelas e imagens.
class ResultadoLaudoHtml
{
    // Limite do HTML gravado por análise. As imagens são guardadas como
    // arquivo e referenciadas por URL, então o HTML em si é pequeno.
    public const TAMANHO_MAXIMO = 1_000_000;

    // Mantém apenas os elementos que o editor produz. Scripts, eventos
    // (onclick...), estilos e imagens externas ou em base64 são removidos;
    // só ficam imagens enviadas pelo próprio sistema (URL relativa).
    public static function sanitizar(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $config = (new HtmlSanitizerConfig())
            ->withMaxInputLength(self::TAMANHO_MAXIMO)
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('sub')
            ->allowElement('sup')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('ul')
            ->allowElement('ol', ['start'])
            ->allowElement('li')
            ->allowElement('blockquote')
            ->allowElement('hr')
            ->allowElement('table')
            ->allowElement('colgroup')
            ->allowElement('col')
            ->allowElement('thead')
            ->allowElement('tbody')
            ->allowElement('tr')
            ->allowElement('th', ['colspan', 'rowspan', 'colwidth'])
            ->allowElement('td', ['colspan', 'rowspan', 'colwidth'])
            ->allowElement('img', ['src', 'alt', 'width', 'height'])
            ->allowMediaSchemes([])
            ->allowRelativeMedias();

        $limpo = (new HtmlSanitizer($config))->sanitize($html);

        // Imagens externas/base64 perdem o src no sanitizador: descarta o <img /> vazio
        $limpo = preg_replace('#<img(?![^>]*\bsrc=)[^>]*>#i', '', $limpo);

        return self::vazio($limpo) ? null : $limpo;
    }

    // Editor vazio gera "<p></p>": sem texto, tabela nem imagem conta como vazio
    public static function vazio(?string $html): bool
    {
        if ($html === null) {
            return true;
        }

        if (preg_match('/<(img|table)\b/i', $html)) {
            return false;
        }

        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')) === '';
    }

    // Versão em texto simples, usada no DOCX da prévia do laudo: parágrafos
    // e linhas de tabela viram " / ", células são separadas por " | " e
    // imagens aparecem como "[imagem]"
    public static function paraTexto(?string $html): string
    {
        if (self::vazio($html)) {
            return '';
        }

        // Marcador de quebra (caractere de controle) para não confundir com
        // barras do próprio texto, como "mg/L"
        $quebra = "\x1F";

        $texto = preg_replace('/<img\b[^>]*>/i', ' [imagem] ', $html);
        // Parágrafo que fecha uma célula não deve virar quebra
        $texto = preg_replace('#</p>\s*(?=</t[dh]>)#i', '', $texto);
        $texto = preg_replace('#</t[dh]>\s*<t[dh]\b[^>]*>#i', ' | ', $texto);
        $texto = preg_replace('#<br\s*/?>|</(p|li|tr|h[1-6]|blockquote)>#i', $quebra, $texto);
        $texto = html_entity_decode(strip_tags($texto), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = preg_replace('/[^\S\x1F]+/u', ' ', $texto);
        $texto = preg_replace('/\s*\x1F[\s\x1F]*/u', $quebra, $texto);

        return str_replace($quebra, ' / ', trim($texto, " \x1F"));
    }
}
