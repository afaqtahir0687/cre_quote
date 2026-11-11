@extends('admin.layouts.app')

@section('title', __('Manage Quotes'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">{{ __('Quotes') }}</h2>
        <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search Name, Quote #...') }}" onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="{{ __('Filter by date') }}" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="{{ __('Clear Filters') }}"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Create New Quote') }}
        </a>
    </div>
</div>

    <x-card :padding="false">
        <x-table>
            <x-slot name="thead">
                <th class="text-nowrap">{{ __('Quote #') }}</th>
                <th style="min-width: 180px;">{{ __('Customer') }}</th>
                <th class="text-nowrap">{{ __('Date') }}</th>
                <th class="text-nowrap">{{ __('Employee') }}</th>
                <th class="text-nowrap">{{ __('Grand Total') }}</th>
                <th class="text-nowrap">{{ __('Status') }}</th>
                <th class="text-nowrap">{{ __('Images') }}</th>
                <th class="text-end text-nowrap">{{ __('Action') }}</th>
            </x-slot>

            @foreach($quotes as $quote)
                <tr class="quote-row align-middle">
                    <td class="fw-bold text-primary text-nowrap">{{ $quote->quote_number }}</td>
                    <td>
                        <div class="fw-semibold text-dark" style="font-size: 0.88rem; line-height: 1.35;">{{ tdb($quote->customer->name ?? '') }}</div>
                        @if(!empty($quote->customer->company_name))
                            <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ tdb($quote->customer->company_name) }}</div>
                        @endif
                        @if(!empty($quote->customer->phone))
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i>{{ $quote->customer->phone }}</div>
                        @endif
                    </td>
                    <td class="text-muted text-nowrap"><span class="d-none">{{ \Carbon\Carbon::parse($quote->quote_date)->format('Y-m-d') }}</span>{{ \Carbon\Carbon::parse($quote->quote_date)->format('d-M-Y') }}</td>
                    <td>
                        @if($quote->employee)
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-user-circle text-primary me-1"></i>{{ tdb($quote->employee->name) }}
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border">
                                <i class="fas fa-user-shield text-muted me-1"></i>{{ __('Admin') }}
                            </span>
                        @endif
                    </td>
                    <td class="fw-semibold text-dark">{{ format_currency($quote->grand_total) }}</td>
                    <td>
                        @php
                            $statusClass = match ($quote->status) {
                                'Accepted' => 'badge-soft-success',
                                'Sent' => 'badge-soft-info',
                                'Invoiced' => 'badge-soft-primary',
                                'Declined' => 'badge-soft-danger',
                                default => 'badge-soft-warning'
                            };
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">
                            {{ __($quote->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $itemImages = $quote->items->pluck('images')->flatten()->filter();
                        @endphp
                        @if($itemImages->count() > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($itemImages->take(3) as $img)
                                    <img src="{{ asset($img) }}" class="rounded border" 
                                        style="width: 32px; height: 32px; object-fit: cover; cursor:pointer;" 
                                        onclick="showImageModal('{{ asset($img) }}')" />
                                @endforeach
                                @if($itemImages->count() > 3)
                                    <span class="badge bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width: 32px; height: 32px; font-size: 0.75rem; cursor: pointer;" 
                                        onclick="showImageModal('{{ asset($itemImages->first()) }}')">
                                        +{{ $itemImages->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">
                                <i class="fas fa-image me-1"></i> {{ __('None') }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('quotes.show', $quote) }}"><i
                                            class="fas fa-eye me-2 text-primary"></i> {{ __('View Detail') }}</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('quotes.edit', $quote) }}"><i
                                            class="fas fa-edit me-2 text-info"></i> {{ __('Edit') }}</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('quotes.pdf', $quote) }}"><i
                                            class="fas fa-file-pdf me-2 text-danger"></i> {{ __('Download PDF') }}</a></li>
                                @if(auth()->user()->role !== 'employee')
                                <li>
                                    <button type="button" class="dropdown-item py-2 assign-emp-trigger"
                                            data-bs-toggle="modal" data-bs-target="#assignQuoteModal"
                                            data-id="{{ $quote->id }}"
                                            data-number="{{ $quote->quote_number }}"
                                            data-current-emp-id="{{ $quote->employee_id ?? '' }}">
                                        <i class="fas fa-user-tag me-2 text-warning"></i> {{ __('Assign / Reclaim') }}
                                    </button>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="fas fa-trash me-2"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                </li>
                                <!-- @if($quote->status != 'Invoiced')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 text-success" href="{{ route('quotes.convert', $quote) }}"><i
                                                class="fas fa-exchange-alt me-2"></i> Convert to Invoice</a></li>
                                @endif -->
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($quotes->hasPages())
            <div class="card-footer bg-transparent border-top p-4">
                {{ $quotes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </x-card>

    <!-- Image Modal (re-used from show page) -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                    <img id="modalImage" src="" class="img-fluid w-100 rounded-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignQuoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-user-tag text-primary me-2"></i> {{ __('Assign Quote') }} <span id="modalQuoteNumber" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignQuoteForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">{{ __('Select an employee to assign this quote to, or choose Admin (Myself / Reclaim) to reclaim full control.') }}</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">{{ __('Assignee / Owner') }}</label>
                            <select name="employee_id" id="modalQuoteEmployeeSelect" class="form-select">
                                <option value="">-- {{ __('Admin (Myself / Reclaim)') }} --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->name }} ({{ $emp->designation ?? 'Staff' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                {{ __('Selecting Admin (Myself / Reclaim) unassigns employees and keeps it under Admin ownership.') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save me-1"></i> {{ __('Save Assignment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const quoteModal = document.getElementById('assignQuoteModal');
            if (quoteModal) {
                const form = document.getElementById('assignQuoteForm');
                const numberSpan = document.getElementById('modalQuoteNumber');
                const select = document.getElementById('modalQuoteEmployeeSelect');

                quoteModal.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    if (!btn) return;
                    const id = btn.getAttribute('data-id');
                    const num = btn.getAttribute('data-number');
                    const empId = btn.getAttribute('data-current-emp-id');

                    numberSpan.textContent = num ? `#${num}` : '';
                    select.value = empId || '';
                    form.action = `/quotes/${id}/assign`;
                });
            }
        });
    </script>
@endsection
