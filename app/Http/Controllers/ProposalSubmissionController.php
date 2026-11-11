<?php

namespace App\Http\Controllers;

use App\Models\CmsSetting;
use App\Models\ProposalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ProposalSubmissionController extends Controller
{
    /**
     * Submit a new 4-step proposal request.
     */
    public function submit(Request $request)
    {
        // 1. Check if proposal form is enabled
        $settings = CmsSetting::where('group', 'proposal')->pluck('value', 'key')->toArray();
        if (isset($settings['proposal_form_enabled']) && $settings['proposal_form_enabled'] === '0') {
            return response()->json([
                'success' => false,
                'message' => __('Proposal requests are currently paused. Please reach out via our contact form.')
            ], 403);
        }

        // 2. Rate Limiting / Duplicate Click Protection (Max 3 submissions per IP per 5 minutes)
        $throttleKey = 'proposal_submit_' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => __("Too many attempts. Please wait {$seconds} seconds before submitting again.")
            ], 429);
        }

        // 3. Strict Server-Side Validation across all 4 steps
        $validated = $request->validate([
            // Step 1: Primary Service
            'service' => 'required|string|max:255',

            // Step 2: Location & Goals
            'customer_location' => 'required|string|max:255',
            'campaign_goal' => 'required|string|max:255',

            // Step 3: Budget & Timeline
            'monthly_budget' => 'required|string|max:255',
            'starting_timeline' => 'required|string|max:255',

            // Step 4: Contact Information
            'website_url' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
        ], [
            'service.required' => __('Please select the primary service you need (Step 1).'),
            'customer_location.required' => __('Please specify your target customer location (Step 2).'),
            'campaign_goal.required' => __('Please choose your primary campaign goal (Step 2).'),
            'monthly_budget.required' => __('Please select an approximate monthly budget (Step 3).'),
            'starting_timeline.required' => __('Please choose when you are looking to get started (Step 3).'),
            'first_name.required' => __('Please enter your first name (Step 4).'),
            'last_name.required' => __('Please enter your last name (Step 4).'),
            'email.required' => __('Please enter a valid email address (Step 4).'),
            'email.email' => __('Please enter a valid email address.'),
            'phone.required' => __('Please enter your contact phone number (Step 4).'),
        ]);

        // Duplicate Check: Same email and service within last 5 minutes
        $recentDuplicate = ProposalRequest::where('email', $validated['email'])
            ->where('service', $validated['service'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentDuplicate) {
            return response()->json([
                'success' => true,
                'reference_no' => $recentDuplicate->reference_no,
                'message' => __("We already received your request! Your reference is #{$recentDuplicate->reference_no}. Our team will contact you shortly.")
            ]);
        }

        RateLimiter::hit($throttleKey, 300);

        // 4. Generate unique reference and save
        $referenceNo = ProposalRequest::generateReferenceNo();

        $proposal = ProposalRequest::create([
            'reference_no' => $referenceNo,
            'service' => $validated['service'],
            'customer_location' => $validated['customer_location'],
            'campaign_goal' => $validated['campaign_goal'],
            'monthly_budget' => $validated['monthly_budget'],
            'starting_timeline' => $validated['starting_timeline'],
            'website_url' => $validated['website_url'] ?? null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ip_address' => $request->ip(),
            'status' => 'Pending',
        ]);

        // 5. Send notification email to admin if configured
        $adminEmail = $settings['proposal_notification_email'] ?? config('mail.from.address', 'creelogics@gmail.com');
        if (!empty($adminEmail)) {
            try {
                Mail::send('emails.proposal_received', ['proposal' => $proposal], function ($m) use ($adminEmail, $proposal) {
                    $m->to($adminEmail)
                      ->subject("New Proposal Request #{$proposal->reference_no} - {$proposal->full_name} ({$proposal->service})");
                });
            } catch (\Exception $e) {
                Log::warning("Could not dispatch proposal notification email: " . $e->getMessage());
            }
        }

        $successMsg = $settings['proposal_success_message'] ?? 'Thank you! Your proposal request has been submitted successfully. Our team will contact you shortly.';

        return response()->json([
            'success' => true,
            'reference_no' => $referenceNo,
            'message' => $successMsg,
            'data' => [
                'reference_no' => $referenceNo,
                'name' => $proposal->full_name,
                'service' => $proposal->service,
                'created_at' => $proposal->created_at->format('M d, Y h:i A'),
            ]
        ]);
    }
}

