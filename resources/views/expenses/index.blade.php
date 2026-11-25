@extends('admin.layouts.app')

@section('title', __('Daily Office Expenses'))

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-wallet text-warning"></i>
                {{ __('Daily Office Expenses') }}
            </h3>
            <p class="text-muted small mb-0">
                @if($isAdmin)
                    {{ __('Monitor and manage daily expenses across all offices, branches, and staff members.') }}
                @else
                    {{ __('Daily expenses for :office (:staff)', ['office' => auth()->user()->shop?->name ?: __('Assigned Office'), 'staff' => auth()->user()->name]) }}
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('expenses.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger fw-semibold d-flex align-items-center gap-2 shadow-xs">
                <i class="fas fa-file-pdf"></i>
                <span>{{ __('Export PDF Report') }}</span>
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>{{ __('Record New Expense') }}</span>
            </a>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __("Today's Expenses") }}</span>
                        <h4 class="fw-bold text-danger mb-0 font-monospace">{{ format_currency($todayTotal) }}</h4>
                        <small class="text-muted extra-small">{{ \Carbon\Carbon::today()->format('d M Y') }}</small>
                    </div>
                    <div class="rounded-3 bg-danger-subtle p-3 text-danger fs-4">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __("This Month's Total") }}</span>
                        <h4 class="fw-bold text-warning-emphasis mb-0 font-monospace">{{ format_currency($monthTotal) }}</h4>
                        <small class="text-muted extra-small">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-3 text-warning-emphasis fs-4">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Filtered Total') }}</span>
                        <h4 class="fw-bold text-primary mb-0 font-monospace">{{ format_currency($totalAmount) }}</h4>
                        <small class="text-muted extra-small">{{ $totalCount }} {{ __('Records found') }}</small>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-3 text-primary fs-4">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">
                            {{ $isAdmin ? __('Active Offices') : __('Your Office') }}
                        </span>
                        <h4 class="fw-bold text-success mb-0">
                            @if($isAdmin)
                                {{ $availableOffices->count() }} {{ __('Offices') }}
                            @else
                                {{ auth()->user()->shop?->name ?: __('Main Office') }}
                            @endif
                        </h4>
                        <small class="text-muted extra-small">{{ __('Business Locations') }}</small>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-3 text-success fs-4">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Panel --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('expenses.index') }}" id="filterForm">
                {{-- Quick Date Preset Pills --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="text-muted extra-small fw-bold text-uppercase me-2"><i class="fas fa-clock me-1"></i>{{ __('Quick Dates') }}:</span>
                    <a href="{{ route('expenses.index', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'page'), ['date_preset' => 'today'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'today' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('Today') }}
                    </a>
                    <a href="{{ route('expenses.index', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'page'), ['date_preset' => 'yesterday'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'yesterday' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('Yesterday') }}
                    </a>
                    <a href="{{ route('expenses.index', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'page'), ['date_preset' => 'this_week'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'this_week' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('This Week') }}
                    </a>
                    <a href="{{ route('expenses.index', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'page'), ['date_preset' => 'this_month'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('This Month') }}
                    </a>
                    <a href="{{ route('expenses.index', request()->except('date_preset', 'start_date', 'end_date', 'page')) }}" 
                       class="btn btn-sm {{ !request('date_preset') && !request('start_date') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('All Dates') }}
                    </a>
                </div>

                <div class="row g-2 align-items-end">
                    {{-- Search Text --}}
                    <div class="col-md-3">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Search Expense') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Title, Ref No, Notes...') }}" value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Office / Branch Filter (Admin Only) --}}
                    @if($isAdmin)
                    <div class="col-md-3">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Office / Branch') }}</label>
                        <select name="shop_id" id="filterShopSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Offices / Branches') }}</option>
                            @foreach($availableOffices as $off)
                                <option value="{{ $off->id }}" {{ request('shop_id') == $off->id ? 'selected' : '' }}>
                                    {{ $off->name }} {{ $off->parent_shop_id ? '(' . __('Branch') . ')' : '(' . __('Main Office') . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Category Filter --}}
                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Category') }}</label>
                        <select name="category" id="filterCategorySelect" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach(\App\Models\Expense::categories() as $k => $lbl)
                                <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Staff Filter (Admin Only) --}}
                    @if($isAdmin)
                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Staff / Added By') }}</label>
                        <select name="user_id" id="filterUserSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Staff') }}</option>
                            @foreach($availableStaff as $stf)
                                <option value="{{ $stf->id }}" {{ request('user_id') == $stf->id ? 'selected' : '' }}>{{ $stf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Custom Date Range --}}
                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('From Date') }}</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('To Date') }}</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-auto d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-filter me-1"></i>{{ __('Filter') }}
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Reset Filters') }}">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Expense Records Table --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Date') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Office / Branch') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Category') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Expense Title & Notes') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Staff') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Payment') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Amount') }}</th>
                            <th class="py-3 text-center text-muted extra-small text-uppercase fw-bold">{{ __('Receipt') }}</th>
                            <th class="pe-3 py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                        <tr>
                            {{-- Date --}}
                            <td class="ps-3 py-3">
                                <div class="fw-bold text-dark">{{ $exp->expense_date->format('d M Y') }}</div>
                                <div class="extra-small text-muted">{{ $exp->expense_date->diffForHumans() }}</div>
                            </td>

                            {{-- Office / Branch --}}
                            <td class="py-3">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    {{ $exp->shop?->name ?: __('Office') }}
                                </span>
                            </td>

                            {{-- Category --}}
                            <td class="py-3">
                                {!! $exp->categoryBadge() !!}
                            </td>

                            {{-- Title & Notes --}}
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                @if($exp->reference_no)
                                    <div class="extra-small text-muted font-monospace"><i class="fas fa-hashtag me-1"></i>Ref: {{ $exp->reference_no }}</div>
                                @endif
                                @if($exp->notes)
                                    <div class="extra-small text-muted text-truncate" style="max-width: 280px;" title="{{ $exp->notes }}">{{ $exp->notes }}</div>
                                @endif
                            </td>

                            {{-- Staff --}}
                            <td class="py-3">
                                <div class="small fw-semibold text-dark">{{ $exp->user?->name ?: __('Staff') }}</div>
                                @if($exp->employee?->designation)
                                    <div class="extra-small text-muted">{{ $exp->employee->designation }}</div>
                                @endif
                            </td>

                            {{-- Payment Method --}}
                            <td class="py-3">
                                <span class="badge bg-light text-secondary border px-2 py-1 small">
                                    {{ $exp->paymentMethodLabel() }}
                                </span>
                            </td>

                            {{-- Amount --}}
                            <td class="py-3 text-end">
                                <span class="fs-6 fw-bold font-monospace text-danger">
                                    {{ format_currency($exp->amount) }}
                                </span>
                            </td>

                            {{-- Receipt / Attachment --}}
                            <td class="py-3 text-center">
                                @if($exp->attachment)
                                    <a href="{{ asset($exp->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 rounded-pill" title="{{ __('View Attached Receipt') }}">
                                        <i class="fas fa-paperclip me-1"></i>{{ __('Bill') }}
                                    </a>
                                @else
                                    <span class="text-muted extra-small">&mdash;</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="pe-3 py-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('expenses.single_pdf', $exp->id) }}" target="_blank" class="btn btn-outline-danger" title="{{ __('Print Voucher PDF') }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-outline-secondary" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="{{ __('Delete') }}" onclick="confirmDeleteExpense({{ $exp->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <form id="delete-expense-{{ $exp->id }}" action="{{ route('expenses.destroy', $exp->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold">{{ __('No Daily Expenses Found') }}</h6>
                                <p class="small text-muted mb-3">{{ __('Start recording daily office expenses like tea, bills, supplies, and maintenance.') }}</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> {{ __('Record First Expense') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.searchable_select')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initSearchableFilterSelect === 'function') {
        if (document.getElementById('filterShopSelect')) {
            window.initSearchableFilterSelect('filterShopSelect', {
                placeholder: "{{ __('All Offices / Branches') }}",
                searchPlaceholder: "{{ __('Search office...') }}"
            });
        }
        if (document.getElementById('filterCategorySelect')) {
            window.initSearchableFilterSelect('filterCategorySelect', {
                placeholder: "{{ __('All Categories') }}",
                searchPlaceholder: "{{ __('Search category...') }}"
            });
        }
        if (document.getElementById('filterUserSelect')) {
            window.initSearchableFilterSelect('filterUserSelect', {
                placeholder: "{{ __('All Staff') }}",
                searchPlaceholder: "{{ __('Search staff member...') }}"
            });
        }
    }
});

function confirmDeleteExpense(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: "{{ __('Delete Expense Record?') }}",
            text: "{{ __('Are you sure you want to delete this expense record? This action cannot be undone.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: "{{ __('Yes, Delete') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-expense-' + id).submit();
            }
        });
    } else {
        if (confirm("{{ __('Are you sure you want to delete this expense record?') }}")) {
            document.getElementById('delete-expense-' + id).submit();
        }
    }
}
</script>
@endpush
@endsection
