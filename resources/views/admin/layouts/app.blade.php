<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}">

    @yield('styles')
</head>

<body>

    @include('partials.sidebar')

    <div class="main-wrapper">
        @include('partials.navbar')

        <main class="main-content">
            {{-- Toast container always present for both server & JS toasts --}}
            <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
                @if(session('success'))
                    <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ $error }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            @if(session('success') || session('error') || $errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var toastElList = [].slice.call(document.querySelectorAll('#toastContainer .toast'));
                    toastElList.forEach(function (toastEl) {
                        new bootstrap.Toast(toastEl).show();
                    });
                });
            </script>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });

        /**
         * Global toast helper — same style as server-rendered toasts.
         * type: 'success' | 'danger' | 'warning' | 'info'
         */
        function showToast(message, type) {
            type = type || 'success';
            var icons = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
            var icon  = icons[type] || 'fa-info-circle';
            var el = document.createElement('div');
            el.className = 'toast align-items-center text-bg-' + type + ' border-0';
            el.setAttribute('role', 'alert');
            el.setAttribute('aria-live', 'assertive');
            el.setAttribute('aria-atomic', 'true');
            el.innerHTML = '<div class="d-flex"><div class="toast-body"><i class="fas ' + icon + ' me-2"></i>' + message + '</div>'
                         + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
            document.getElementById('toastContainer').appendChild(el);
            var t = new bootstrap.Toast(el, { delay: 4000 });
            t.show();
            el.addEventListener('hidden.bs.toast', function () { el.remove(); });
        }

        // Global form submit loader
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName === 'FORM' && form.checkValidity()) {
                var submitBtns = form.querySelectorAll('button[type="submit"]');
                submitBtns.forEach(function(btn) {
                    if (!btn.disabled) {
                        btn.disabled = true;
                        // Avoid adding spinner twice
                        if (!btn.innerHTML.includes('spinner-border')) {
                            btn.dataset.originalHtml = btn.innerHTML;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + btn.innerHTML;
                        }
                    }
                });
            }
        });
    </script>
        <script>
        let searchTimeout = null;

        function bindSearchEvents() {
            const searchInputs = document.querySelectorAll('form[method="GET"] input[name="search"]');
            
            searchInputs.forEach(input => {
                // Remove old event listener if exists to prevent duplicates (though we replace DOM anyway)
                const new_input = input.cloneNode(true);
                input.parentNode.replaceChild(new_input, input);

                new_input.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const form = new_input.closest('form');
                        const url = new URL(form.action);
                        url.searchParams.set('search', new_input.value);
                        
                        // Show loading state (optional)
                        new_input.style.opacity = '0.6';
                        
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                
                                const currentMain = document.querySelector('main');
                                const newMain = doc.querySelector('main');
                                
                                if (currentMain && newMain) {
                                    // Save focus state
                                    const activeElement = document.activeElement;
                                    const isSearchFocused = activeElement && activeElement.name === 'search';
                                    let selectionStart = 0, selectionEnd = 0;
                                    
                                    if (isSearchFocused) {
                                        selectionStart = activeElement.selectionStart;
                                        selectionEnd = activeElement.selectionEnd;
                                    }

                                    // Replace main content
                                    currentMain.innerHTML = newMain.innerHTML;

                                    // Re-bind events
                                    bindSearchEvents();

                                    // Restore focus
                                    if (isSearchFocused) {
                                        const restoredInput = document.querySelector('form[method="GET"] input[name="search"]');
                                        if (restoredInput) {
                                            restoredInput.focus();
                                            restoredInput.setSelectionRange(selectionStart, selectionEnd);
                                        }
                                    }
                                }
                            })
                            .catch(err => {
                                console.error('Search error:', err);
                                new_input.style.opacity = '1';
                            });
                    }, 500); // 500ms debounce
                });

                // Prevent form submission on enter since it's auto-searching
                new_input.closest('form').addEventListener('submit', function(e) {
                    e.preventDefault();
                });
            });
        }

        document.addEventListener('DOMContentLoaded', bindSearchEvents);
    </script>
    @yield('scripts')

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/996598500143" target="_blank" style="position: fixed; width: 60px; height: 60px; bottom: 40px; right: 40px; background-color: #25d366; color: #FFF; border-radius: 50px; text-align: center; font-size: 35px; box-shadow: 2px 2px 5px rgba(0,0,0,0.3); z-index: 1000; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        <i class="fab fa-whatsapp"></i>
    </a>
    <script>
        document.addEventListener('keydown', function(event) {
            // Check if the pressed key is Enter
            if (event.key === 'Enter') {
                const target = event.target;
                
                // If it's a textarea, let Enter work natively (new line)
                if (target.tagName === 'TEXTAREA') {
                    return;
                }
                
                // If it's an explicitly type="submit" button or standard button inside a form, let it submit
                if (target.tagName === 'BUTTON' || target.type === 'submit') {
                    return;
                }
                
                // If the target is inside a form, prevent the form from submitting
                if (target.closest('form')) {
                    event.preventDefault();
                }
            }
        });
    </script>
</body>

</html>

</html>




