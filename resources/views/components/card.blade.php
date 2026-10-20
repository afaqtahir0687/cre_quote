@props(['title' => null, 'footer' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card-modern']) }}>
    @if($title)
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold text-dark">{{ $title }}</h5>
            @if(isset($headerActions))
                <div>{{ $headerActions }}</div>
            @endif
        </div>
    @endif

    <div @class(['p-0' => !$padding])>
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer bg-transparent border-top mt-4 pt-3">
            {{ $footer }}
        </div>
    @endif
</div>
