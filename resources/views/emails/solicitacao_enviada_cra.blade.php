<x-mail::message>
# Nova solicitação de serviço

O solicitante **{{ $solicitante }}** enviou a solicitação de serviço **{{ $descricao ?: 'sem descrição' }}** para o CRA.

<x-mail::table>
| | |
|:--|:--|
| **Número** | {{ $numero ?: '—' }} |
| **Solicitante** | {{ $solicitante }} |
| **Descrição** | {{ $descricao ?: '—' }} |
| **Enviada em** | {{ $data }} |
</x-mail::table>

<x-mail::button :url="$url">
Ver solicitação
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
