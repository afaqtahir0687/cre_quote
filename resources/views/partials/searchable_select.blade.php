@once
@push('styles')
<style>
    /* Antigravity Unified Searchable Dropdown Engine */
    .filter-dropdown { position: relative; width: 100%; }
    .filter-trigger {
        cursor: pointer;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.25rem !important;
        font-weight: 500;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    [dir="rtl"] .filter-trigger {
        background-position: left 0.75rem center !important;
        padding-right: 0.75rem !important;
        padding-left: 2.25rem !important;
    }
    .filter-trigger:focus, .filter-trigger.active {
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.2) !important;
        border-color: #86b7fe !important;
    }
    .filter-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; min-width: 100%; max-width: 100%;
        z-index: 1060; background: #fff; border: 1px solid #dee2e6;
        border-radius: 0.5rem; box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden;
    }
    [dir="rtl"] .filter-panel {
        left: auto;
        right: 0;
    }
    .filter-panel.open { display: block; animation: filterDropdownIn .12s ease-in-out; }
    @keyframes filterDropdownIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .filter-search-wrap { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; background: #f8f9fa; }
    .filter-search {
        width: 100%; border: 1px solid #dee2e6; border-radius: 0.375rem;
        padding: 6px 10px 6px 32px; font-size: .875rem; outline: none;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%236c757d' viewBox='0 0 16 16'%3e%3cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10'/%3e%3c/svg%3e") no-repeat 10px center;
    }
    [dir="rtl"] .filter-search {
        padding: 6px 32px 6px 10px;
        background-position: right 10px center;
    }
    .filter-search:focus { border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
    .filter-list { max-height: 230px; overflow-y: auto; padding: 4px 0; }
    .filter-list::-webkit-scrollbar { width: 5px; }
    .filter-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }
    .filter-option {
        padding: 8px 14px; font-size: .88rem; cursor: pointer;
        border-left: 3px solid transparent; transition: background .12s, border-color .12s;
        display: flex; justify-content: space-between; align-items: center;
    }
    [dir="rtl"] .filter-option {
        border-left: none;
        border-right: 3px solid transparent;
    }
    .filter-option:hover, .filter-option.focused { background: #f0f4ff; border-left-color: #0d6efd; }
    [dir="rtl"] .filter-option:hover, [dir="rtl"] .filter-option.focused { border-right-color: #0d6efd; }
    .filter-option.selected { background: #e7f0ff; border-left-color: #0d6efd; font-weight: 600; }
    [dir="rtl"] .filter-option.selected { border-right-color: #0d6efd; }
    .filter-option.selected .check-icon { display: inline-block; color: #0d6efd; }
    .filter-option .check-icon { display: none; }
    .filter-empty { padding: 14px; text-align: center; color: #6c757d; font-size: .875rem; }
    .filter-optgroup-label {
        padding: 6px 14px; font-size: .75rem; font-weight: bold; text-transform: uppercase;
        color: #6c757d; background: #fafafa;
    }
</style>
@endpush

@push('scripts')
<script>
window.initSearchableFilterSelect = function(selectOrId, options = {}) {
    const select = typeof selectOrId === 'string' ? document.getElementById(selectOrId) : selectOrId;
    if (!select || select.dataset.filterEnhanced === 'true') return;
    select.dataset.filterEnhanced = 'true';

    const wrapper = document.createElement('div');
    wrapper.className = 'filter-dropdown';
    if (options.wrapperClass) wrapper.classList.add(options.wrapperClass);

    const trigger = document.createElement('input');
    trigger.type = 'text';
    trigger.className = 'form-control filter-trigger';
    if (select.classList.contains('form-select-sm') || select.classList.contains('form-control-sm')) {
        trigger.classList.add('form-control-sm');
    }
    if (select.classList.contains('is-invalid')) {
        trigger.classList.add('is-invalid');
    }
    trigger.readOnly = true;
    trigger.placeholder = options.placeholder || (select.options[0]?.text || '{{ __("Select...") }}');

    const panel = document.createElement('div');
    panel.className = 'filter-panel';

    const searchWrap = document.createElement('div');
    searchWrap.className = 'filter-search-wrap';

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'filter-search';
    searchInput.placeholder = options.searchPlaceholder || '{{ __("Type to search...") }}';

    searchWrap.appendChild(searchInput);
    panel.appendChild(searchWrap);

    const listEl = document.createElement('div');
    listEl.className = 'filter-list';
    panel.appendChild(listEl);

    const emptyEl = document.createElement('div');
    emptyEl.className = 'filter-empty';
    emptyEl.style.display = 'none';
    emptyEl.textContent = options.emptyText || '{{ __("No matching results found") }}';
    listEl.appendChild(emptyEl);

    wrapper.appendChild(trigger);
    wrapper.appendChild(panel);

    select.parentNode.insertBefore(wrapper, select.nextSibling);
    select.style.setProperty('display', 'none', 'important');

    const items = [];

    function buildOptions() {
        while (listEl.firstChild && listEl.firstChild !== emptyEl) {
            listEl.removeChild(listEl.firstChild);
        }
        items.length = 0;

        Array.from(select.children).forEach(function(child) {
            if (child.tagName === 'OPTGROUP') {
                const groupLabel = document.createElement('div');
                groupLabel.className = 'filter-optgroup-label';
                groupLabel.textContent = child.label;
                listEl.insertBefore(groupLabel, emptyEl);

                Array.from(child.children).forEach(function(opt) {
                    addOptionItem(opt, groupLabel);
                });
            } else if (child.tagName === 'OPTION') {
                addOptionItem(child, null);
            }
        });

        syncFromSelect();
    }

    function addOptionItem(opt, groupHeader) {
        const itemEl = document.createElement('div');
        itemEl.className = 'filter-option';
        itemEl.dataset.value = opt.value;

        const nameSpan = document.createElement('span');
        nameSpan.className = 'opt-name';
        nameSpan.textContent = opt.text;

        const icon = document.createElement('i');
        icon.className = 'fas fa-check check-icon';

        itemEl.appendChild(nameSpan);
        itemEl.appendChild(icon);
        listEl.insertBefore(itemEl, emptyEl);

        const itemObj = {
            el: itemEl,
            opt: opt,
            value: opt.value,
            text: opt.text,
            groupHeader: groupHeader,
            searchStr: (opt.text + ' ' + opt.value).toLowerCase()
        };

        itemEl.addEventListener('click', function(e) {
            e.stopPropagation();
            selectOption(itemObj);
        });

        items.push(itemObj);
    }

    function selectOption(itemObj) {
        select.value = itemObj.value;
        syncFromSelect();
        closePanel();
        select.dispatchEvent(new Event('change', { bubbles: true }));
        if (typeof select.onchange === 'function') {
            select.onchange();
        }
        if (options.onSelect) {
            options.onSelect(itemObj.value, itemObj.text, itemObj.opt);
        }
    }

    function syncFromSelect() {
        const curVal = select.value;
        let selectedItem = null;

        items.forEach(function(item) {
            if (item.value === curVal || (item.value === '' && !curVal)) {
                if (!selectedItem) {
                    selectedItem = item;
                    item.el.classList.add('selected');
                } else {
                    item.el.classList.remove('selected');
                }
            } else {
                item.el.classList.remove('selected');
            }
        });

        if (selectedItem) {
            trigger.value = selectedItem.text;
        } else if (select.selectedIndex >= 0 && select.options[select.selectedIndex]) {
            trigger.value = select.options[select.selectedIndex].text;
        } else {
            trigger.value = '';
        }
    }

    function hl(text, q) {
        if (!text) return '';
        if (!q) return escapeHtml(text);
        const rx = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return escapeHtml(text).replace(rx, '<mark style="background:#fff3cd;padding:0 2px;border-radius:2px;font-weight:700;">$1</mark>');
    }

    function escapeHtml(s) {
        return (s || '').replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function filterOptions(q) {
        q = (q || '').toLowerCase().trim();
        let visibleCount = 0;

        items.forEach(function(item) {
            const isMatch = !q || item.searchStr.indexOf(q) > -1;
            item.el.style.display = isMatch ? 'flex' : 'none';
            const nameSpan = item.el.querySelector('.opt-name');
            if (nameSpan) {
                nameSpan.innerHTML = isMatch && q ? hl(item.text, q) : escapeHtml(item.text);
            }
            if (isMatch) visibleCount++;
        });

        listEl.querySelectorAll('.filter-optgroup-label').forEach(function(gh) {
            let hasVisible = false;
            let sibling = gh.nextElementSibling;
            while (sibling && !sibling.classList.contains('filter-optgroup-label') && sibling !== emptyEl) {
                if (sibling.style.display !== 'none') {
                    hasVisible = true;
                    break;
                }
                sibling = sibling.nextElementSibling;
            }
            gh.style.display = hasVisible ? 'block' : 'none';
        });

        emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function openPanel() {
        document.querySelectorAll('.filter-panel.open').forEach(p => p !== panel && p.classList.remove('open'));
        document.querySelectorAll('.filter-trigger.active').forEach(t => t !== trigger && t.classList.remove('active'));

        panel.classList.add('open');
        trigger.classList.add('active');
        searchInput.value = '';
        filterOptions('');
        setTimeout(() => searchInput.focus(), 50);
    }

    function closePanel() {
        panel.classList.remove('open');
        trigger.classList.remove('active');
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.contains('open') ? closePanel() : openPanel();
    });

    searchInput.addEventListener('input', function() {
        filterOptions(this.value);
    });

    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePanel();
    });

    select.addEventListener('change', function() {
        syncFromSelect();
    });

    const observer = new MutationObserver(function() {
        buildOptions();
    });
    observer.observe(select, { childList: true, subtree: true, characterData: true });

    buildOptions();
    return wrapper;
};
</script>
@endpush
@endonce
