<div class="table-responsive-lg" style="min-height: 250px;">
    <table {{ $attributes->merge(['class' => 'table-modern']) }}>
        <thead>
            <tr>
                {{ $thead }}
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

