@extends('admin.layouts.app')

@section('title', __('Manage Delivery Notes'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">{{ __('Delivery Notes') }}</h2>
            <div class="d-flex align-items-center gap-3">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search DN...') }}" onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="{{ __('Filter by date') }}" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="{{ __('Clear Filters') }}"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('delivery_notes.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i> {{ __('Create Delivery Note') }}
        </a>
    </div>
    </div>

    <x-card :padding="false">
        <x-table>
            <x-slot name="thead">
                <th class="text-nowrap">{{ __('Delivery #') }}</th>
                <th class="text-nowrap">{{ __('Date') }}</th>
                <th style="min-width: 180px;">{{ __('Customer') }}</th>
                <th class="text-nowrap">{{ __('Invoice #') }}</th>
                <th class="text-nowrap">{{ __('Employee') }}</th>
                <th class="text-nowrap">{{ __('Status') }}</th>
                <th class="text-end text-nowrap">{{ __('Action') }}</th>
            </x-slot>

            @foreach($deliveryNotes as $deliveryNote)
                <tr class="align-middle">
                    <td class="fw-bold text-primary text-nowrap">{{ $deliveryNote->delivery_number }}</td>
                    <td class="text-muted text-nowrap">{{ $deliveryNote->delivery_date }}</td>
                    <td>
                        <div class="fw-semibold text-dark" style="font-size: 0.88rem; line-height: 1.35;">{{ tdb($deliveryNote->customer->name ?? '') }}</div>
                        @if(!empty($deliveryNote->customer->company_name))
                            <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ tdb($deliveryNote->customer->company_name) }}</div>
                        @endif
                        @if(!empty($deliveryNote->customer->phone))
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i>{{ $deliveryNote->customer->phone }}</div>
                        @endif
                    </td>
                    <td class="fw-semibold text-dark">{{ $deliveryNote->invoice->invoice_number }}</td>
                    <td>
                        @if($deliveryNote->employee)
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-user-circle text-primary me-1"></i>{{ tdb($deliveryNote->employee->name) }}
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border">
                                <i class="fas fa-user-shield text-muted me-1"></i>{{ __('Admin') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match ($deliveryNote->status) {
                                'Delivered' => 'badge-soft-success',
                                'Pending' => 'badge-soft-warning',
                                'Cancelled' => 'badge-soft-danger',
                                default => 'badge-soft-secondary'
                            };
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">
                            {{ __($deliveryNote->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.show', $deliveryNote) }}"><i
                                            class="fas fa-eye me-2 text-primary"></i> {{ __('View Detail') }}</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.edit', $deliveryNote) }}"><i
                                            class="fas fa-edit me-2 text-info"></i> {{ __('Edit') }}</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.pdf', $deliveryNote) }}"><i
                                            class="fas fa-file-pdf me-2 text-danger"></i> {{ __('Download PDF') }}</a></li>
                                @if(auth()->user()->role !== 'employee')
                                <li>
                                    <button type="button" class="dropdown-item py-2 assign-emp-trigger"
                                            data-bs-toggle="modal" data-bs-target="#assignDeliveryNoteModal"
                                            data-id="{{ $deliveryNote->id }}"
                                            data-number="{{ $deliveryNote->delivery_number }}"
                                            data-current-emp-id="{{ $deliveryNote->employee_id ?? '' }}">
                                        <i class="fas fa-user-tag me-2 text-warning"></i> {{ __('Assign / Reclaim') }}
                                    </button>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('delivery_notes.update_status', $deliveryNote) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to mark this delivery note as Delivered?">
                                        @csrf
                                        <input type="hidden" name="status" value="Delivered">
                                        <button type="submit" class="dropdown-item py-2 text-success">
                                            <i class="fas fa-check me-2"></i> {{ __('Mark Delivered') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($deliveryNotes->hasPages())
            <div class="card-footer bg-transparent border-top p-4">
                {{ $deliveryNotes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </x-card>

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignDeliveryNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-user-tag text-primary me-2"></i> {{ __('Assign Delivery Note') }} <span id="modalDeliveryNoteNumber" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignDeliveryNoteForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">{{ __('Select an employee to assign this delivery note to, or choose Admin (Myself / Reclaim) to reclaim full control.') }}</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">{{ __('Assignee / Owner') }}</label>
                            <select name="employee_id" id="modalDeliveryNoteEmployeeSelect" class="form-select">
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
    document.addEventListener('DOMContentLoaded', function() {
        const dnModal = document.getElementById('assignDeliveryNoteModal');
        if (dnModal) {
            const form = document.getElementById('assignDeliveryNoteForm');
            const numberSpan = document.getElementById('modalDeliveryNoteNumber');
            const select = document.getElementById('modalDeliveryNoteEmployeeSelect');

            dnModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const num = btn.getAttribute('data-number');
                const empId = btn.getAttribute('data-current-emp-id');

                numberSpan.textContent = num ? `#${num}` : '';
                select.value = empId || '';
                form.action = `/delivery_notes/${id}/assign`;
            });
        }
    });
</script>
@endsection
