@if (session('success'))
    <div class="sca-alert sca-alert--success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="sca-alert sca-alert--error">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="sca-alert sca-alert--error">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif