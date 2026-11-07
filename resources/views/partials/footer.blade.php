<footer class="app-footer">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div>
            <span>&copy; {{ date('Y') }} <strong>{{ platform_brand_name() }}</strong>. {{ __('All rights reserved.') }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-muted border px-2 py-1">
                <i class="fas fa-shield-alt text-primary me-1"></i> {{ __('Enterprise SaaS') }} v2.4
            </span>
            @if(current_shop())
                <span class="text-muted small">
                    <i class="fas fa-store text-secondary me-1"></i>{{ current_shop()->name }}
                </span>
            @endif
        </div>
    </div>
</footer>

