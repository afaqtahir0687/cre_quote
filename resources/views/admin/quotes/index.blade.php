@extends('admin.layouts.app')

@section('title', 'Manage Quotes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">Quotes</h2>
        <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search Name, Quote #..." onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="Filter by date" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="Clear Filters"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> Create New Quote
        </a>
    </div>
</div>

    <x-card :padding="false">
        <x-table>
            <x-slot name="thead">
                <th>Quote #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Employee</th>
                <th>Grand Total</th>
                <th>Status</th>
                <th>Images</th>
                <th class="text-end">Action</th>
            </x-slot>

            @foreach($quotes as $quote)
                <tr class="quote-row">
                    <td class="fw-bold text-primary">{{ $quote->quote_number }}</td>
                    <td>
                        <div class="fw-bold">{{ $quote->customer->name ?? '' }}</div>
                        @if(!empty($quote->customer->company_name))
                            <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ $quote->customer->company_name }}</div>
                        @endif
                        @if(!empty($quote->customer->phone))
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i>{{ $quote->customer->phone }}</div>
                        @endif
                    </td>
                    <td class="text-muted text-nowrap"><span class="d-none">{{ \Carbon\Carbon::parse($quote->quote_date)->format('Y-m-d') }}</span>{{ \Carbon\Carbon::parse($quote->quote_date)->format('d-M-Y') }}</td>
                    <td>
                        @if($quote->employee)
                            <div class="d-flex align-items-center">
                                <span class="text-muted small">{{ $quote->employee->name }}</span>
                            </div>
                        @else
                            <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">N/A</span>
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
                            {{ $quote->status }}
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
                                <i class="fas fa-image me-1"></i> None
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
                                            class="fas fa-eye me-2 text-primary"></i> View Detail</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('quotes.edit', $quote) }}"><i
                                            class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('quotes.pdf', $quote) }}"><i
                                            class="fas fa-file-pdf me-2 text-danger"></i> Download PDF</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                <!-- @if($quote->status != 'Invoiced')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 text-success" href="{{ route('quotes.convert', $quote) }}"
                                            onclick="return confirm('Convert this quote to an Invoice?')"><i
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
    
    function filterTable() {
        let textFilter = document.getElementById('searchInput').value.toLowerCase();
        let dateFilter = document.getElementById('dateFilter').value;
        let rows = document.querySelectorAll('.quote-row');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase().replace(/\s+/g, ' ');
            let textMatch = text.includes(textFilter);
            let dateMatch = dateFilter === '' || text.includes(dateFilter);
            
            if (textMatch && dateMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('dateFilter').addEventListener('change', filterTable);
</script>
@endsection