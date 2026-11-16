@props([
    'id' => 'phone_picker',
    'name' => 'phone',
    'codeName' => 'country_code',
    'value' => '',
    'defaultCode' => '+966',
    'defaultIso' => 'sa',
    'required' => true,
    'placeholder' => '50 123 4567',
    'error' => null,
])

@php
    $countries = \App\Models\Customer::allCountries();
    $currentCode = old($codeName, $defaultCode);
    $currentIso = $defaultIso;

    // Detect ISO from currentCode
    foreach ($countries as $iso => $c) {
        if ($c['code'] === $currentCode) {
            $currentIso = $iso;
            break;
        }
    }
@endphp

@once
<style>
.country-phone-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: stretch !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    background: #ffffff !important;
    position: relative;
    width: 100%;
}
.country-phone-group:focus-within {
    border-color: #4361EE !important;
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.18) !important;
}
.country-phone-group.has-error {
    border-color: #dc3545 !important;
}
.country-phone-group .dropdown {
    display: flex !important;
    align-items: stretch !important;
    flex-shrink: 0 !important;
}
.country-picker-btn {
    background: #f8fafc !important;
    border: none !important;
    border-right: 1px solid #ced4da !important;
    color: #212529 !important;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem;
    border-top-left-radius: 0.375rem !important;
    border-bottom-left-radius: 0.375rem !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    white-space: nowrap !important;
    cursor: pointer;
    box-shadow: none !important;
    outline: none !important;
    min-height: 38px;
    transition: background 0.15s ease;
}
.country-picker-btn:hover, 
.country-picker-btn:focus, 
.country-picker-btn.show {
    background: #e9ecef !important;
    color: #4361EE !important;
}
.country-phone-group .phone-input {
    flex: 1 1 auto !important;
    border: none !important;
    border-radius: 0 0.375rem 0.375rem 0 !important;
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem;
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
    color: #212529 !important;
    min-height: 38px;
    width: 100%;
}
.country-phone-group .phone-input:focus {
    box-shadow: none !important;
    border: none !important;
    outline: none !important;
}
.country-picker-btn .fi {
    width: 20px;
    height: 15px;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    background-size: cover;
    background-position: center;
    display: inline-block;
    flex-shrink: 0;
}
.country-picker-menu {
    border-radius: 0.5rem !important;
    border: 1px solid #ced4da !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    z-index: 1060 !important;
    width: 300px !important;
    max-width: 90vw;
    padding: 8px !important;
}
.country-search-wrap {
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 4px;
}
.country-list-scroll {
    max-height: 240px;
    overflow-y: auto;
}
.country-list-scroll::-webkit-scrollbar { width: 5px; }
.country-list-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.country-item {
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.84rem;
    display: flex;
    align-items: center;
    color: #212529;
    transition: background 0.12s ease;
}
.country-item:hover {
    background: #eef2ff;
    color: #4361EE;
}
.country-item.active {
    background: #eef2ff;
    color: #4361EE;
    font-weight: 600;
}
.country-item .fi {
    width: 18px;
    height: 14px;
    border-radius: 2px;
    flex-shrink: 0;
    margin-right: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.12);
}
.country-item .c-name {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.country-item .c-code {
    color: #6c757d;
    font-size: 0.78rem;
    margin-left: 8px;
    font-weight: 600;
}

/* RTL overrides */
[dir="rtl"] .country-picker-btn {
    border-right: none !important;
    border-left: 1px solid #ced4da !important;
    border-top-right-radius: 0.375rem !important;
    border-bottom-right-radius: 0.375rem !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
[dir="rtl"] .country-phone-group .phone-input {
    border-radius: 0.375rem 0 0 0.375rem !important;
    direction: ltr;
    text-align: right;
}
[dir="rtl"] .country-item .fi {
    margin-right: 0;
    margin-left: 8px;
}
[dir="rtl"] .country-item .c-code {
    margin-left: 0;
    margin-right: 8px;
}
</style>
@endonce

<div class="country-phone-group @if($error) has-error @endif" id="{{ $id }}_group">
    <div class="dropdown">
        <button class="country-picker-btn dropdown-toggle" 
                type="button" 
                id="{{ $id }}_btn" 
                data-bs-toggle="dropdown" 
                data-bs-auto-close="outside"
                aria-expanded="false">
            <span class="fi fi-{{ $currentIso }}" id="{{ $id }}_flag"></span>
            <span class="country-code-text fw-semibold ms-1" id="{{ $id }}_code_text">{{ $currentCode }}</span>
        </button>
        <div class="dropdown-menu country-picker-menu p-2 shadow-lg" aria-labelledby="{{ $id }}_btn">
            <div class="country-search-wrap">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 country-search-input" id="{{ $id }}_search" placeholder="{{ __('Search country or code...') }}" autocomplete="off">
                </div>
            </div>
            <div class="country-list-scroll" id="{{ $id }}_list">
                @foreach($countries as $iso => $c)
                    <div class="country-item {{ $iso === $currentIso ? 'active' : '' }}" 
                         data-iso="{{ $iso }}" 
                         data-code="{{ $c['code'] }}" 
                         data-name="{{ $c['name'] }}">
                        <span class="fi fi-{{ $iso }}"></span>
                        <span class="c-name">{{ $c['name'] }}</span>
                        <span class="c-code">{{ $c['code'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <input type="hidden" name="{{ $codeName }}" id="{{ $id }}_code" value="{{ $currentCode }}">
    <input type="tel" 
           name="{{ $name }}" 
           id="{{ $id }}_input" 
           class="form-control phone-input @if($error) is-invalid @endif" 
           value="{{ old($name, $value) }}" 
           placeholder="{{ $placeholder }}" 
           autocomplete="tel"
           {{ $required ? 'required' : '' }}>
</div>

<script>
(function() {
    function initPicker_{{ str_replace('-', '_', $id) }}() {
        var group = document.getElementById('{{ $id }}_group');
        if (!group || group.dataset.initialized) return;
        group.dataset.initialized = 'true';

        var btn = document.getElementById('{{ $id }}_btn');
        var flag = document.getElementById('{{ $id }}_flag');
        var codeText = document.getElementById('{{ $id }}_code_text');
        var hiddenCode = document.getElementById('{{ $id }}_code');
        var phoneInput = document.getElementById('{{ $id }}_input');
        var searchInput = document.getElementById('{{ $id }}_search');
        var list = document.getElementById('{{ $id }}_list');
        var items = list.querySelectorAll('.country-item');

        btn.addEventListener('shown.bs.dropdown', function () {
            searchInput.value = '';
            items.forEach(function(el) { el.style.display = ''; });
            setTimeout(function(){ searchInput.focus(); }, 50);
        });

        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            items.forEach(function(item) {
                var name = item.dataset.name.toLowerCase();
                var code = item.dataset.code.toLowerCase();
                var iso = item.dataset.iso.toLowerCase();
                if (!q || name.indexOf(q) > -1 || code.indexOf(q) > -1 || iso.indexOf(q) > -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var iso = this.dataset.iso;
                var code = this.dataset.code;
                flag.className = 'fi fi-' + iso;
                codeText.textContent = code;
                hiddenCode.value = code;
                items.forEach(function(i) { i.classList.remove('active'); });
                this.classList.add('active');
                var dd = bootstrap.Dropdown.getInstance(btn);
                if (dd) dd.hide();
                phoneInput.focus();
            });
        });

        window['setCountry_{{ str_replace("-", "_", $id) }}'] = function(targetCode, targetIso) {
            targetCode = targetCode || '+966';
            targetIso = targetIso || 'sa';
            flag.className = 'fi fi-' + targetIso;
            codeText.textContent = targetCode;
            hiddenCode.value = targetCode;
            items.forEach(function(i) {
                var active = (i.dataset.iso === targetIso || i.dataset.code === targetCode);
                i.classList.toggle('active', active);
            });
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPicker_{{ str_replace('-', '_', $id) }});
    } else {
        initPicker_{{ str_replace('-', '_', $id) }}();
    }
})();
</script>
