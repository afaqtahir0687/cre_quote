<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\Quote;
use App\Models\Setting;
use App\Models\Department;
use App\Models\JobAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\PDF; // PDF generation
use App\Models\JobOrderPayment;

class JobOrderController extends Controller
{
    private function syncInvoicePayments($jobOrder)
    {
        if ($jobOrder->status === 'cancelled') {
            return;
        }

        if ($jobOrder->invoice_id) {
            $invoice = \App\Models\Invoice::find($jobOrder->invoice_id);
            if ($invoice) {
                // Delete only the auto-imported advance payments from Job Order
                $invoice->payments()->where('note', 'like', 'Advance payment from Job Order #' . $jobOrder->order_number)->delete();
                
                // Re-import all current Job Order payments
                foreach ($jobOrder->payments as $payment) {
                    $invoice->payments()->create([
                        'payment_date' => $payment->received_at ? \Carbon\Carbon::parse($payment->received_at)->format('Y-m-d') : now()->format('Y-m-d'),
                        'amount' => $payment->amount,
                        'payment_method' => $payment->method,
                        'transaction_id' => $payment->reference,
                        'note' => 'Advance payment from Job Order #' . $jobOrder->order_number,
                    ]);
                }
                
                // Recalculate invoice totals
                $totalPaid = $invoice->payments()->sum('amount');
                $newBalanceDue = max(0, $invoice->grand_total - $totalPaid);
                $invoice->update([
                    'amount_paid' => $totalPaid,
                    'balance_due' => $newBalanceDue,
                    'status' => ($newBalanceDue <= 0 && $invoice->grand_total > 0) ? 'Paid' : ($totalPaid > 0 ? 'Partially Paid' : 'Unpaid'),
                ]);
            }
        }
    }

    private function syncWithInvoice($jobOrder)
    {
        if ($jobOrder->status === 'cancelled') {
            return;
        }

        if ($jobOrder->invoice_id) {
            $invoice = \App\Models\Invoice::find($jobOrder->invoice_id);
            if ($invoice) {
                // 1. Sync payments between job order and invoice
                $this->syncInvoicePayments($jobOrder);
                $invoice->refresh();

                // 2. Sync totals & items
                $newGrandTotal = (float) ($jobOrder->grand_total ?? $jobOrder->total_amount);
                $totalPaid = (float) $invoice->payments()->sum('amount');
                $newBalanceDue = max(0, $newGrandTotal - $totalPaid);

                $invoice->update([
                    'subtotal' => $jobOrder->subtotal ?? $jobOrder->total_amount,
                    'total_tax' => $jobOrder->total_tax ?? 0,
                    'total_discount' => $jobOrder->total_discount ?? 0,
                    'shipping' => $jobOrder->shipping ?? 0,
                    'grand_total' => $newGrandTotal,
                    'amount_paid' => $totalPaid,
                    'balance_due' => $newBalanceDue,
                    'status' => ($newBalanceDue <= 0 && $newGrandTotal > 0) ? 'Paid' : ($totalPaid > 0 ? 'Partially Paid' : 'Unpaid'),
                    'notes' => $jobOrder->notes,
                ]);

                // Sync items
                $invoice->items()->delete();
                foreach ($jobOrder->items as $item) {
                    $invoice->items()->create([
                        'description' => $item->description,
                        'item_text' => $item->item_text,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'tax_percentage' => $item->tax_percentage ?? 0,
                        'discount' => $item->discount ?? 0,
                        'total' => $item->total,
                    ]);
                }

                // Keep jobOrder payment_status and paid_amount aligned
                $jobOrder->update([
                    'paid_amount' => $totalPaid,
                    'payment_status' => ($newBalanceDue <= 0 && $newGrandTotal > 0) ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending'),
                ]);
            }
        }
    }
    /**
     * Show the form for creating a new job order.
     */
    public function create()
    {
        $customers = \App\Models\Customer::where('is_active', true)->latest()->get();
        $setting = current_setting();
        $orderNumber = \App\Helpers\SerialGenerator::generate('job_order', now()->format('Y-m-d'));
        
        return view('admin.job_orders.create_direct', compact('customers', 'setting', 'orderNumber'));
    }

    /**
     * Store a newly created job order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'subtotal' => 'required|numeric',
            'total_tax' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'shipping' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer',
            'items.*.description' => 'required|string',
            'items.*.item_text' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.tax_percentage' => 'required|numeric',
            'items.*.discount' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'items.*.existing_images' => 'nullable|string',
              'items.*.removed_images' => 'nullable|string',
              'items.*.images' => 'nullable|array',
            'items.*.images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20480',
        ]);

        $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
        $validated['employee_id'] = $employee ? $employee->id : null;
        $validated['order_date'] = $validated['order_date'] ?? now();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['total_amount'] = $validated['grand_total'];
        $validated['paid_amount'] = 0;
        $validated['remaining_balance'] = $validated['grand_total'];

        $dateForSerial = $validated['order_date'] ?? now()->format('Y-m-d');
        $validated['order_number'] = \App\Helpers\SerialGenerator::generate('job_order', $dateForSerial);

        $jobOrder = JobOrder::create($validated);

        foreach ($validated['items'] as $item) {
            $itemData = [
                'description' => $item['description'],
                'item_text' => $item['item_text'] ?? null,
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'tax_percentage' => $item['tax_percentage'],
                'discount' => $item['discount'],
                'total' => $item['total'],
            ];

            // Handle image upload - store directly in public/uploads/job_order_items
            $itemData['images'] = [];
            if (isset($item['images']) && is_array($item['images'])) {
                foreach ($item['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $imageName = \App\Helpers\ImageHelper::compressAndSave($image, 'uploads/job_order_items');
                        $itemData['images'][] = 'uploads/job_order_items/' . $imageName;
                    }
                }
            }

            $jobOrder->items()->create($itemData);
        }

        return redirect()->route('job-orders.show', $jobOrder->id)
            ->with('success', 'Job Order created successfully.');
    }

    /**
     * Create a Job Order from a Quote (copy data & assign departments).
     */
    public function storeFromQuote(Request $request, $quoteId)
    {
        $quote = Quote::with('items')->findOrFail($quoteId);
        // Mark quote as Accepted / Approved
        $quote->update(['status' => 'Accepted']);

        $orderNumber = \App\Helpers\SerialGenerator::generate('job_order', now()->format('Y-m-d'));

        $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
        $employeeId = $employee ? $employee->id : null;

        $jobOrder = JobOrder::create([
            'order_number' => $orderNumber,
            'quote_id' => $quote->id,
            'customer_id' => $quote->customer_id,
            'employee_id' => $employeeId,
            'order_date' => now(),
            'status' => 'approved',
            'notes' => $quote->notes ?? '',
            'subtotal' => $quote->subtotal,
            'total_tax' => $quote->total_tax,
            'total_discount' => $quote->total_discount,
            'shipping' => $quote->shipping,
            'grand_total' => $quote->grand_total,
            'total_amount' => $quote->grand_total,
            'paid_amount' => 0,
            'remaining_balance' => $quote->grand_total,
        ]);

        // Copy items
        foreach ($quote->items as $item) {
            JobOrderItem::create([
                'job_order_id' => $jobOrder->id,
                'description' => $item->description,
                'item_text' => $item->item_text,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_percentage' => $item->tax_percentage,
                'discount' => $item->discount,
                'total' => $item->total,
                'images' => $item->images,
            ]);
        }

        // Handle department assignments if submitted
        if ($request->has('departments') && is_array($request->departments)) {
            foreach ($request->departments as $deptData) {
                if (empty($deptData['department_id']) && empty($deptData['custom_name'])) {
                    continue;
                }
                $deptName = null;
                if (!empty($deptData['department_id'])) {
                    $dept = Department::find($deptData['department_id']);
                    $deptName = $dept ? $dept->name : null;
                }
                if (empty($deptName)) {
                    $deptName = $deptData['custom_name'] ?? 'General Work';
                }

                JobAssignment::create([
                    'job_order_id' => $jobOrder->id,
                    'department_id' => $deptData['department_id'] ?? null,
                    'department_name' => $deptName,
                    'employee_id' => $deptData['employee_id'] ?? null,
                    'assigned_to_name' => $deptData['assigned_to_name'] ?? null,
                    'instructions' => $deptData['instructions'] ?? null,
                    'due_date' => $deptData['due_date'] ?? null,
                    'status' => 'pending',
                ]);
            }
        }

        // Ensure total amount matches grand total
        $total = $jobOrder->items()->sum('total');
        $jobOrder->total_amount = $jobOrder->grand_total ?? $total;
        $jobOrder->save();

        return redirect()->route('job-orders.show', $jobOrder->id)
            ->with('success', 'Quotation approved & Job Order created with department assignments.');
    }

    /** Index - List all Job Orders */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = JobOrder::with('customer', 'items', 'employee', 'invoice');

        if (auth()->user()->role === 'employee') {
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('order_date', $request->date);
        }

        $jobOrders = $query->latest('id')->paginate(10)->withQueryString();

        return view('admin.job_orders.index', compact('jobOrders'));
    }

    /** Show a Job Order */
    public function show($id)
    {
        $jobOrder = JobOrder::with(['items', 'employee', 'customer', 'assignments.department', 'assignments.employee'])->findOrFail($id);
        $setting = ($jobOrder->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $jobOrder->shop_id)->first() : null) ?: current_setting();
        $departments = Department::where('is_active', true)->get();
        $employees = Employee::all();
        return view('admin.job_orders.show', compact('jobOrder', 'setting', 'departments', 'employees'));
    }

    /** Add department work assignment to a Job Order */
    public function addAssignment(Request $request, $id)
    {
        $jobOrder = JobOrder::findOrFail($id);

        if ($jobOrder->status === 'cancelled') {
            return back()->with('error', 'Cannot assign work to a cancelled Job Order.');
        }

        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'department_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'assigned_to_name' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $departmentName = null;
        if (!empty($validated['department_id'])) {
            $dept = Department::find($validated['department_id']);
            $departmentName = $dept ? $dept->name : null;
        }
        if (empty($departmentName)) {
            $departmentName = $validated['department_name'] ?? 'General Work';
        }

        JobAssignment::create([
            'job_order_id' => $jobOrder->id,
            'department_id' => $validated['department_id'] ?? null,
            'department_name' => $departmentName,
            'employee_id' => $validated['employee_id'] ?? null,
            'assigned_to_name' => $validated['assigned_to_name'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Work assignment added successfully.');
    }

    /** Update status of a work assignment */
    public function updateAssignmentStatus(Request $request, $assignmentId)
    {
        $assignment = JobAssignment::findOrFail($assignmentId);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'in_progress' && !$assignment->started_at) {
            $updateData['started_at'] = now();
        } elseif ($validated['status'] === 'completed') {
            if (!$assignment->started_at) {
                $updateData['started_at'] = now();
            }
            $updateData['completed_at'] = now();
        }

        $assignment->update($updateData);

        return back()->with('success', 'Assignment status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])));
    }

    /** Delete assignment */
    public function deleteAssignment($assignmentId)
    {
        $assignment = JobAssignment::findOrFail($assignmentId);
        $assignment->delete();

        return back()->with('success', 'Work assignment deleted successfully.');
    }

    /** Edit form */
    public function edit($id)
    {
        $jobOrder = JobOrder::with('items')->findOrFail($id);
        $setting = ($jobOrder->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $jobOrder->shop_id)->first() : null) ?: current_setting();
        $customers = \App\Models\Customer::where('is_active', true)->latest()->get();
        return view('admin.job_orders.edit', compact('jobOrder', 'setting', 'customers'));
    }

    /** Update Job Order */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'subtotal' => 'required|numeric',
            'total_tax' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'shipping' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer',
            'items.*.description' => 'required|string',
            'items.*.item_text' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.tax_percentage' => 'required|numeric',
            'items.*.discount' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'items.*.existing_images' => 'nullable|string',
              'items.*.removed_images' => 'nullable|string',
              'items.*.images' => 'nullable|array',
            'items.*.images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20480',
        ]);

        $jobOrder = JobOrder::with(['items', 'payments', 'invoice'])->findOrFail($id);

        $isCancelled = (isset($validated['status']) && $validated['status'] === 'cancelled');

        if ($isCancelled) {
            $validated['subtotal'] = 0;
            $validated['total_tax'] = 0;
            $validated['total_discount'] = 0;
            $validated['shipping'] = 0;
            $validated['grand_total'] = 0;
            $validated['total_amount'] = 0;
            $validated['paid_amount'] = 0;
            $validated['remaining_balance'] = 0;
            $validated['payment_status'] = 'cancelled';
            $newGrandTotal = 0;
            $paidAmount = 0;
        } else {
            $newGrandTotal = (float) $validated['grand_total'];
            $validated['total_amount'] = $newGrandTotal;

            // Calculate paid amount from existing payments (or linked invoice payments)
            $paidAmount = (float) $jobOrder->payments()->sum('amount');
            if ($jobOrder->invoice_id && $jobOrder->invoice) {
                $paidAmount = (float) $jobOrder->invoice->payments()->sum('amount');
            }
            $validated['paid_amount'] = $paidAmount;

            // Recalculate payment status and balance
            $remaining = max(0, $newGrandTotal - $paidAmount);
            $validated['payment_status'] = ($remaining <= 0 && $newGrandTotal > 0) ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');
        }
        $validated['updated_by'] = auth()->id();

        if (isset($validated['order_date'])) {
            $newDate = \Carbon\Carbon::parse($validated['order_date'])->format('Ymd');
            $currentDateStr = $jobOrder->order_date ? \Carbon\Carbon::parse($jobOrder->order_date)->format('Ymd') : '';
            
            if ($newDate !== $currentDateStr) {
                $validated['order_number'] = \App\Helpers\SerialGenerator::generate('job_order', $validated['order_date']);
            }
        }

        $jobOrder->update($validated);
        $jobOrder->touch();

        // Track item IDs to keep
        $keepItemIds = [];

        // Create or update items
        foreach ($validated['items'] as $item) {
            $itemData = [
                'description' => $item['description'],
                'item_text' => $item['item_text'] ?? null,
                'unit_price' => $isCancelled ? 0 : $item['unit_price'],
                'quantity' => $item['quantity'],
                'tax_percentage' => $isCancelled ? 0 : $item['tax_percentage'],
                'discount' => $isCancelled ? 0 : $item['discount'],
                'total' => $isCancelled ? 0 : $item['total'],
            ];

            $itemData['images'] = [];
            $existingImages = [];
            if (isset($item['existing_images']) && !empty($item['existing_images'])) {
                $existingImages = json_decode($item['existing_images'], true) ?? [];
            }
            if (isset($item['removed_images']) && !empty($item['removed_images'])) {
                $removedImages = json_decode($item['removed_images'], true) ?? [];
                $existingImages = array_diff($existingImages, $removedImages);
            }
            $itemData['images'] = array_values($existingImages);
            if (isset($item['images']) && is_array($item['images'])) {
                foreach ($item['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $imageName = \App\Helpers\ImageHelper::compressAndSave($image, 'uploads/job_order_items');
                        $itemData['images'][] = 'uploads/job_order_items/' . $imageName;
                    }
                }
            }

            if (isset($item['id']) && $item['id']) {
                $existingItem = $jobOrder->items()->find($item['id']);
                if ($existingItem) {
                    $existingItem->update($itemData);
                    $keepItemIds[] = $existingItem->id;
                    continue;
                }
            }
            
            $newItem = $jobOrder->items()->create($itemData);
            $keepItemIds[] = $newItem->id;
        }

        // Delete removed items
        $jobOrder->items()->whereNotIn('id', $keepItemIds)->delete();

        // If cancelled, remove payments, cancel assignments, and sync/cancel linked invoice
        if ($isCancelled) {
            $jobOrder->payments()->delete();
            $jobOrder->assignments()->whereIn('status', ['pending', 'in_progress'])->update([
                'status' => 'cancelled',
            ]);
            if ($jobOrder->invoice_id && $jobOrder->invoice) {
                $invoice = $jobOrder->invoice;
                // Delete ALL payments on this linked invoice (both advance payments and direct invoice payments)
                $invoice->payments()->delete();
                $invoice->update([
                    'status' => 'Cancelled',
                    'subtotal' => 0,
                    'total_tax' => 0,
                    'total_discount' => 0,
                    'shipping' => 0,
                    'grand_total' => 0,
                    'amount_paid' => 0,
                    'balance_due' => 0,
                ]);
                $invoice->items()->update([
                    'unit_price' => 0,
                    'tax_percentage' => 0,
                    'discount' => 0,
                    'total' => 0,
                ]);
                \App\Models\DeliveryNote::where('invoice_id', $invoice->id)->update(['status' => 'Cancelled']);
            }
        }

        // Reload fresh relations
        $jobOrder->load(['items', 'payments', 'invoice']);

        // Sync with linked invoice if it exists and not cancelled
        if ($jobOrder->invoice_id && !$isCancelled) {
            $this->syncWithInvoice($jobOrder);
        }

        return redirect()->route('job-orders.show', $jobOrder->id)
            ->with('success', 'Job Order updated successfully.');
    }
    /** Convert Job Order to Invoice */
    public function generateInvoice($id)
    {
        $jobOrder = JobOrder::with('items', 'customer', 'payments')->findOrFail($id);

        if ($jobOrder->status === 'cancelled') {
            return back()->with('error', 'Cannot convert a cancelled Job Order to an Invoice.');
        }

        $setting = ($jobOrder->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $jobOrder->shop_id)->first() : null) ?: current_setting();

        // Update payment status if fully paid
        if ($jobOrder->remaining_balance <= 0 && $jobOrder->payment_status !== 'paid') {
            $jobOrder->payment_status = 'paid';
            $jobOrder->save();
        }

        // Generate Invoice Number from Job Order Number
        $prefix = $setting->invoice_prefix ?? 'INV-';
        if (preg_match('/^JO-?(.*)/i', $jobOrder->order_number, $matches)) {
            $invoiceNumber = $prefix . $matches[1];
        } else {
            $invoiceNumber = $prefix . $jobOrder->order_number;
        }

        if ($jobOrder->invoice_id) {
            $invoice = \App\Models\Invoice::find($jobOrder->invoice_id);
            if ($invoice) {
                // Check if Job Order was updated AFTER invoice was updated
                $jobUpdated = $jobOrder->updated_at;
                $itemsUpdated = $jobOrder->items()->max('updated_at');
                $lastUpdate = max($jobUpdated, $itemsUpdated);

                if ($lastUpdate <= $invoice->updated_at) {
                    return back()->with('error', 'Already taken! No new updates in Job Order to sync to the invoice.');
                }

                \Illuminate\Support\Facades\DB::transaction(function () use ($jobOrder, $invoice) {
                    $newGrandTotal = $jobOrder->grand_total ?? $jobOrder->total_amount;
                    $newBalanceDue = max(0, $newGrandTotal - $invoice->amount_paid);
                    
                    $invoice->update([
                        'subtotal' => $jobOrder->subtotal ?? $jobOrder->total_amount,
                        'total_tax' => $jobOrder->total_tax ?? 0,
                        'total_discount' => $jobOrder->total_discount ?? 0,
                        'shipping' => $jobOrder->shipping ?? 0,
                        'grand_total' => $newGrandTotal,
                        'balance_due' => $newBalanceDue,
                        'status' => ($newBalanceDue <= 0 && $newGrandTotal > 0) ? 'Paid' : ($invoice->amount_paid > 0 ? 'Partially Paid' : 'Unpaid'),
                        'notes' => $jobOrder->notes,
                    ]);

                    $invoice->items()->delete();
                    foreach ($jobOrder->items as $item) {
                        $invoice->items()->create([
                            'description' => $item->description,
                            'item_text' => $item->item_text,
                            'unit_price' => $item->unit_price,
                            'quantity' => $item->quantity,
                            'tax_percentage' => $item->tax_percentage ?? 0,
                            'discount' => $item->discount ?? 0,
                            'total' => $item->total,
                        ]);
                    }
                });

                return redirect()->route('invoices.show', $invoice->id)
                    ->with('success', 'Invoice updated successfully with new Job Order changes.');
            }
        }

        // If no invoice is linked, but the invoice number is already taken (even in recycle bin)
        if (\App\Models\Invoice::withTrashed()->where('invoice_number', $invoiceNumber)->exists()) {
            return back()->with('error', 'Already taken! An invoice with serial number ' . $invoiceNumber . ' already exists (check Recycle Bin).');
        }

        $invoice = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($jobOrder, $invoiceNumber, &$invoice) {
            $invoice = \App\Models\Invoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'customer_id' => $jobOrder->customer_id,
                'employee_id' => $jobOrder->employee_id,
                'subtotal' => $jobOrder->subtotal ?? $jobOrder->total_amount,
                'total_tax' => $jobOrder->total_tax ?? 0,
                'total_discount' => $jobOrder->total_discount ?? 0,
                'shipping' => $jobOrder->shipping ?? 0,
                'grand_total' => $jobOrder->grand_total ?? $jobOrder->total_amount,
                'amount_paid' => $jobOrder->paid_amount,
                'balance_due' => $jobOrder->remaining_balance,
                'status' => ($jobOrder->remaining_balance <= 0 && ($jobOrder->grand_total ?? $jobOrder->total_amount) > 0) ? 'Paid' : ($jobOrder->paid_amount > 0 ? 'Partially Paid' : 'Unpaid'),
                'notes' => $jobOrder->notes,
            ]);

            foreach ($jobOrder->payments as $payment) {
                $invoice->payments()->create([
                    'payment_date' => $payment->received_at ? \Carbon\Carbon::parse($payment->received_at)->format('Y-m-d') : now()->format('Y-m-d'),
                    'amount' => $payment->amount,
                    'payment_method' => $payment->method,
                    'transaction_id' => $payment->reference,
                    'note' => 'Advance payment from Job Order #' . $jobOrder->order_number,
                ]);
            }

            foreach ($jobOrder->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'item_text' => $item->item_text,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'tax_percentage' => $item->tax_percentage ?? 0,
                    'discount' => $item->discount ?? 0,
                    'total' => $item->total,
                ]);
            }

            $jobOrder->update([
                'status' => 'completed',
                'invoice_id' => $invoice->id
            ]);
        });

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Job Order successfully converted to Invoice.');
    }

    /** Store a new payment for a Job Order */
    public function addPayment(Request $request, $id)
    {
        $jobOrder = JobOrder::findOrFail($id);

        if ($jobOrder->status === 'cancelled') {
            return back()->with('error', 'Cannot add payment to a cancelled Job Order.');
        }

        $effectiveTotal = (float) ($jobOrder->grand_total ?? $jobOrder->total_amount);
        if ($jobOrder->paid_amount >= $effectiveTotal && $effectiveTotal > 0) {
            return back()->with('error', 'This Job Order is already fully paid.');
        }

        $validated = $request->validate([
            'method' => 'required|in:cash,bank,check,other',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
        ]);

        JobOrderPayment::create([
            'job_order_id' => $jobOrder->id,
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'reference' => $validated['reference'] ?? null,
            'received_at' => now(),
        ]);

        // Recalculate totals
        $jobOrder->paid_amount = $jobOrder->payments()->sum('amount');
        $remaining = max(0, $effectiveTotal - $jobOrder->paid_amount);
        $jobOrder->payment_status = ($remaining <= 0 && $effectiveTotal > 0) ? 'paid' : ($jobOrder->paid_amount > 0 ? 'partial' : 'pending');
        $jobOrder->save();
        
        $this->syncInvoicePayments($jobOrder);

        return back()->with('success', 'Payment added and synced with Invoice successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $payment = \App\Models\JobOrderPayment::findOrFail($id);
        $jobOrder = $payment->jobOrder;

        $validated = $request->validate([
            'method' => 'required|in:cash,bank,check,other',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);

        $payment->update([
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'reference' => $validated['reference'] ?? null,
        ]);

        // Recalculate totals
        $jobOrder->paid_amount = $jobOrder->payments()->sum('amount');
        $effectiveTotal = (float) ($jobOrder->grand_total ?? $jobOrder->total_amount);
        $remaining = max(0, $effectiveTotal - $jobOrder->paid_amount);
        $jobOrder->payment_status = ($remaining <= 0 && $effectiveTotal > 0) ? 'paid' : ($jobOrder->paid_amount > 0 ? 'partial' : 'pending');
        $jobOrder->save();

        $this->syncInvoicePayments($jobOrder);

        return back()->with('success', 'Payment updated and synced with Invoice successfully.');
    }

    public function deletePayment($id)
    {
        $payment = \App\Models\JobOrderPayment::findOrFail($id);
        $jobOrder = $payment->jobOrder;
        
        $payment->delete();

        // Recalculate totals
        $jobOrder->paid_amount = $jobOrder->payments()->sum('amount');
        $effectiveTotal = (float) ($jobOrder->grand_total ?? $jobOrder->total_amount);
        $remaining = max(0, $effectiveTotal - $jobOrder->paid_amount);
        $jobOrder->payment_status = ($remaining <= 0 && $effectiveTotal > 0) ? 'paid' : ($jobOrder->paid_amount > 0 ? 'partial' : 'pending');
        $jobOrder->save();

        $this->syncInvoicePayments($jobOrder);

        return back()->with('success', 'Payment deleted and synced with Invoice successfully.');
    }

    /** Download Job Order PDF */
    public function downloadPdf($id)
    {
        $jobOrder = JobOrder::with(['items', 'payments', 'customer', 'employee', 'assignments.department', 'assignments.employee'])->findOrFail($id);
        $setting = ($jobOrder->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $jobOrder->shop_id)->first() : null) ?: current_setting();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $html = view('admin.job_orders.pdf', compact('jobOrder', 'setting'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $jobOrder->order_number . '.pdf"');
    }

    /** Download Statement PDF for Job Orders */
    public function downloadStatementPdf(Request $request)
    {
        $query = JobOrder::with(['customer', 'items', 'employee', 'invoice', 'payments']);

        if (auth()->user()->role === 'employee') {
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $dateFilter = null;
        if ($request->filled('date')) {
            $dateFilter = $request->date;
            $query->whereDate('order_date', $request->date);
        }

        $jobOrders = $query->orderBy('order_date', 'asc')->orderBy('id', 'asc')->get();

        if ($jobOrders->isEmpty()) {
            return back()->with('error', 'No Job Orders found' . ($dateFilter ? ' for ' . \Carbon\Carbon::parse($dateFilter)->format('d-M-Y') : '') . ' to generate statement.');
        }

        $setting = current_setting();
        $userTz = request()->cookie('user_timezone', config('app.timezone'));
        view()->share('userTz', $userTz);

        $totalOrders = $jobOrders->count();
        $totalBilled = $jobOrders->sum(function($jo) { return (float)($jo->grand_total ?? $jo->total_amount); });
        $totalPaid = $jobOrders->sum(function($jo) { return (float)$jo->paid_amount; });
        $totalBalance = $jobOrders->sum(function($jo) { return (float)$jo->remaining_balance; });
        $totalDiscount = $jobOrders->sum(function($jo) { return (float)$jo->total_discount; });

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $html = view('admin.job_orders.statement_pdf', compact(
            'jobOrders', 'setting', 'dateFilter', 'totalOrders', 'totalBilled', 'totalPaid', 'totalBalance', 'totalDiscount'
        ))->render();

        $mpdf->WriteHTML($html);

        $fileName = 'Job_Orders_Statement_' . ($dateFilter ? \Carbon\Carbon::parse($dateFilter)->format('d_M_Y') : 'All') . '.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /** Cancel a Job Order and reset all its amounts to zero */
    public function cancel($id)
    {
        $jobOrder = JobOrder::with(['items', 'payments', 'invoice', 'assignments', 'customer'])->findOrFail($id);

        if ($jobOrder->status === 'cancelled') {
            return back()->with('info', 'This Job Order is already cancelled.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($jobOrder) {
            // 1. Reset all Job Order financial fields to 0 and set status to cancelled
            $jobOrder->update([
                'status' => 'cancelled',
                'subtotal' => 0,
                'total_tax' => 0,
                'total_discount' => 0,
                'shipping' => 0,
                'grand_total' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'remaining_balance' => 0,
                'payment_status' => 'cancelled',
                'updated_by' => auth()->id(),
            ]);

            // 2. Reset all item amounts to 0
            $jobOrder->items()->update([
                'unit_price' => 0,
                'tax_percentage' => 0,
                'discount' => 0,
                'total' => 0,
            ]);

            // 3. Delete payments recorded for this job order
            $jobOrder->payments()->delete();

            // 4. Cancel pending and in_progress department assignments
            $jobOrder->assignments()->whereIn('status', ['pending', 'in_progress'])->update([
                'status' => 'cancelled',
            ]);

            // 5. If linked to an invoice, sync/cancel invoice
            if ($jobOrder->invoice_id && $jobOrder->invoice) {
                $invoice = $jobOrder->invoice;
                // Delete ALL payments on this linked invoice (both advance payments and direct invoice payments)
                $invoice->payments()->delete();
                $invoice->update([
                    'status' => 'Cancelled',
                    'subtotal' => 0,
                    'total_tax' => 0,
                    'total_discount' => 0,
                    'shipping' => 0,
                    'grand_total' => 0,
                    'amount_paid' => 0,
                    'balance_due' => 0,
                ]);
                $invoice->items()->update([
                    'unit_price' => 0,
                    'tax_percentage' => 0,
                    'discount' => 0,
                    'total' => 0,
                ]);
                \App\Models\DeliveryNote::where('invoice_id', $invoice->id)->update(['status' => 'Cancelled']);
            }

            // 6. Log the cancellation
            \App\Models\SystemLog::create([
                'user_id' => auth()->id(),
                'action' => 'Cancelled',
                'model_type' => 'JobOrder',
                'description' => "Cancelled Job Order #{$jobOrder->order_number} (Customer: " . ($jobOrder->customer->name ?? 'N/A') . ") - All totals and amounts set to zero."
            ]);
        });

        return back()->with('success', 'Job Order #' . $jobOrder->order_number . ' has been cancelled and all amounts set to zero.');
    }

    /** Delete Job Order */
    public function destroy($id)
    {
        $jobOrder = JobOrder::findOrFail($id);
        $orderNumber = $jobOrder->order_number;
        $customerName = $jobOrder->customer->name ?? 'Unknown';

        $jobOrder->delete();

        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'JobOrder',
            'description' => "Deleted Job Order #{$orderNumber} (Customer: {$customerName})"
        ]);

        return redirect()->route('job-orders.index')
            ->with('success', 'Job Order deleted successfully.');
    }

}

?>








