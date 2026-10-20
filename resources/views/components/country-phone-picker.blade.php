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
                    <input type="text" class="form-control border-start-0 country-search-input" id="{{ $id }}_search" placeholder="Search country or code..." autocomplete="off">
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
