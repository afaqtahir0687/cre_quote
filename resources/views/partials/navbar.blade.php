<nav class="top-navbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-link link-dark d-lg-none me-3" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mb-0 text-dark fw-bold">@yield('title', 'Welcome')</h4>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="search-box d-none d-md-block">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 shadow-none" placeholder="Search...">
            </div>
        </div>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                
                <span class="d-none d-md-inline text-dark fw-medium">{{ auth()->check() ? Auth::user()->name : 'Guest' }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" aria-labelledby="userDropdown">
                @if(auth()->check() && Auth::user()->role === 'admin')
                    <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i
                            class="fas fa-user-circle me-2"></i>Settings</a></li>
                @endif
                
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger"><i
                                class="fas fa-sign-out-alt me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>