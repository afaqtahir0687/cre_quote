<div class="table-responsive">
    <table class="table-modern align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 60px;">#</th>
                <th>{{ __('Option Name & Details') }}</th>
                <th style="width: 140px;">{{ __('Icon') }}</th>
                <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                <th style="width: 80px;" class="text-end">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($options as $opt)
                <tr>
                    <td class="text-muted small">
                        <span class="badge-soft badge-soft-secondary fw-bold">#{{ $opt->sort_order }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $opt->name }}</div>
                        @if($opt->subtitle)
                            <div class="text-muted small">{{ $opt->subtitle }}</div>
                        @endif
                    </td>
                    <td>
                        @if($opt->icon)
                            <span class="badge-soft badge-soft-primary px-2 py-1">
                                <i class="{{ $opt->icon }} me-1"></i> {{ $opt->icon }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('super_admin.cms.proposals.options.toggle_active', $opt->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="border-0 bg-transparent p-0 shadow-none cursor-pointer" title="{{ $opt->is_active ? __('Click to deactivate') : __('Click to activate') }}">
                                @if($opt->is_active)
                                    <span class="badge-soft badge-soft-success">
                                        <i class="fas fa-check-circle me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge-soft badge-soft-secondary">
                                        Inactive
                                    </span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('super_admin.cms.proposals.options.destroy', $opt->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-action btn-action-delete" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted small">
                        {{ __('No options configured yet.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
