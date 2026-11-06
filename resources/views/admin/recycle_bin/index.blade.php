@extends('admin.layouts.app')
@section('title', __('Recycle Bin'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">{{ __('Recycle Bin') }}</h2>
    </div>

    <div class="row">
        <!-- Customers -->
        <div class="col-12 mb-4">
            <x-card title="{{ __('Deleted Customers') }}" :padding="false">
                <x-table>
                    <x-slot name="thead">
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Deleted At') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </x-slot>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <form action="{{ route('recycle_bin.restore') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="Customer">
                                    <input type="hidden" name="id" value="{{ $customer->id }}">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-trash-restore"></i> {{ __('Restore') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('No deleted customers found.') }}</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Job Orders -->
        <div class="col-12 mb-4">
            <x-card title="{{ __('Deleted Job Orders') }}" :padding="false">
                <x-table>
                    <x-slot name="thead">
                        <th>#</th>
                        <th>{{ __('Job Order #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Deleted At') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </x-slot>
                    @forelse($jobOrders as $jobOrder)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jobOrder->order_number }}</td>
                            <td>{{ $jobOrder->customer->name ?? 'N/A' }}</td>
                            <td>{{ $jobOrder->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <form action="{{ route('recycle_bin.restore') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="JobOrder">
                                    <input type="hidden" name="id" value="{{ $jobOrder->id }}">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-trash-restore"></i> {{ __('Restore') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('No deleted job orders found.') }}</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Invoices -->
        <div class="col-12 mb-4">
            <x-card title="{{ __('Deleted Invoices') }}" :padding="false">
                <x-table>
                    <x-slot name="thead">
                        <th>#</th>
                        <th>{{ __('Invoice #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Deleted At') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </x-slot>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <form action="{{ route('recycle_bin.restore') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="Invoice">
                                    <input type="hidden" name="id" value="{{ $invoice->id }}">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-trash-restore"></i> {{ __('Restore') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('No deleted invoices found.') }}</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Quotes -->
        <div class="col-12 mb-4">
            <x-card title="{{ __('Deleted Quotes') }}" :padding="false">
                <x-table>
                    <x-slot name="thead">
                        <th>#</th>
                        <th>{{ __('Quote #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Deleted At') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </x-slot>
                    @forelse($quotes as $quote)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $quote->quote_number }}</td>
                            <td>{{ $quote->customer->name ?? 'N/A' }}</td>
                            <td>{{ $quote->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <form action="{{ route('recycle_bin.restore') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="Quote">
                                    <input type="hidden" name="id" value="{{ $quote->id }}">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-trash-restore"></i> {{ __('Restore') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('No deleted quotes found.') }}</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>
    </div>
</div>
@endsection
