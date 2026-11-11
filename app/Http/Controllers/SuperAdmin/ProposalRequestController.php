<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProposalRequest;
use App\Models\ProposalOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProposalRequestController extends Controller
{
    /**
     * Display a listing of proposal requests with filters.
     */
    public function index(Request $request)
    {
        $query = ProposalRequest::query();

        // Search by keyword (Name, Email, Phone, Reference No)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('website_url', 'like', "%{$search}%");
            });
        }

        // Filter by Service
        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date From / To
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $proposals = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Metrics for Quick Stats
        $stats = [
            'total' => ProposalRequest::count(),
            'pending' => ProposalRequest::where('status', 'Pending')->count(),
            'in_discussion' => ProposalRequest::whereIn('status', ['Contacted', 'In Discussion', 'Proposal Sent'])->count(),
            'converted' => ProposalRequest::where('status', 'Converted')->count(),
        ];

        $services = ProposalOption::ofType('service')->active()->pluck('name');
        $statuses = ['Pending', 'Contacted', 'In Discussion', 'Proposal Sent', 'Converted', 'Rejected'];

        return view('super_admin.proposals.index', compact('proposals', 'stats', 'services', 'statuses'));
    }

    /**
     * Display the specified proposal request details.
     */
    public function show(ProposalRequest $proposal)
    {
        $statuses = ['Pending', 'Contacted', 'In Discussion', 'Proposal Sent', 'Converted', 'Rejected'];
        return view('super_admin.proposals.show', compact('proposal', 'statuses'));
    }

    /**
     * Update proposal status and internal notes.
     */
    public function updateStatus(Request $request, ProposalRequest $proposal)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Contacted,In Discussion,Proposal Sent,Converted,Rejected',
            'internal_notes' => 'nullable|string',
        ]);

        $proposal->status = $request->status;
        if ($request->has('internal_notes')) {
            $proposal->internal_notes = $request->internal_notes;
            $proposal->notes_updated_at = now();
            $proposal->notes_updated_by = auth()->id();
        }
        $proposal->save();

        return back()->with('success', __("Proposal #{$proposal->reference_no} status updated to {$proposal->status}."));
    }

    /**
     * Remove the specified proposal request.
     */
    public function destroy(ProposalRequest $proposal)
    {
        $ref = $proposal->reference_no;
        $proposal->delete();

        return redirect()->route('super_admin.proposals.index')->with('success', __("Proposal #{$ref} deleted successfully."));
    }

    /**
     * Export proposals to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = ProposalRequest::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="proposals_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Reference No',
                'Date & Time',
                'Status',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Website URL',
                'Primary Service',
                'Target Location',
                'Campaign Goal',
                'Monthly Budget',
                'Starting Timeline',
                'Internal Notes'
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->reference_no,
                    $r->created_at->format('Y-m-d H:i:s'),
                    $r->status,
                    $r->first_name,
                    $r->last_name,
                    $r->email,
                    $r->phone,
                    $r->website_url,
                    $r->service,
                    $r->customer_location,
                    $r->campaign_goal,
                    $r->monthly_budget,
                    $r->starting_timeline,
                    $r->internal_notes,
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}

