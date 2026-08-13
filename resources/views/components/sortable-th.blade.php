@props(['column', 'sort', 'direction'])

@php
    $isActive = $sort === $column;
    $nextDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => 1]);
@endphp

<th>
    <a href="{{ $url }}" class="sca-table__sort {{ $isActive ? 'is-active' : '' }}">
        {{ $slot }}
        <span class="sca-table__sort-icon">
            @if($isActive)
                {{ $direction === 'asc' ? '▲' : '▼' }}
            @else
                ⇅
            @endif
        </span>
    </a>
</th>
