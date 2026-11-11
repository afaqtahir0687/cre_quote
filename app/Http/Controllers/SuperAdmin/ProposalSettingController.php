<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\ProposalOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProposalSettingController extends Controller
{
    /**
     * Display proposal form settings and options management.
     */
    public function index()
    {
        $settings = CmsSetting::where('group', 'proposal')->pluck('value', 'key')->toArray();

        $services = ProposalOption::ofType('service')->orderBy('sort_order')->get();
        $locations = ProposalOption::ofType('location')->orderBy('sort_order')->get();
        $goals = ProposalOption::ofType('goal')->orderBy('sort_order')->get();
        $budgets = ProposalOption::ofType('budget')->orderBy('sort_order')->get();
        $timelines = ProposalOption::ofType('timeline')->orderBy('sort_order')->get();

        return view('super_admin.proposals.settings', compact('settings', 'services', 'locations', 'goals', 'budgets', 'timelines'));
    }

    /**
     * Update general proposal form settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'proposal_form_enabled' => 'nullable|boolean',
            'proposal_modal_title' => 'required|string|max:255',
            'proposal_modal_subtitle' => 'nullable|string|max:500',
            'proposal_notification_email' => 'nullable|email|max:255',
            'proposal_submit_btn_text' => 'required|string|max:100',
            'proposal_success_message' => 'required|string|max:500',
        ]);

        $settings = [
            'proposal_form_enabled' => $request->boolean('proposal_form_enabled') ? '1' : '0',
            'proposal_modal_title' => $request->proposal_modal_title,
            'proposal_modal_subtitle' => $request->proposal_modal_subtitle,
            'proposal_notification_email' => $request->proposal_notification_email,
            'proposal_submit_btn_text' => $request->proposal_submit_btn_text,
            'proposal_success_message' => $request->proposal_success_message,
        ];

        foreach ($settings as $key => $val) {
            CmsSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'group' => 'proposal']
            );
        }

        Cache::forget('cms_all_settings');

        return back()->with('success', __('Proposal form settings saved successfully.'));
    }

    /**
     * Store a new proposal option (service, location, goal, budget, timeline).
     */
    public function storeOption(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:service,location,goal,budget,timeline',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $order = $request->sort_order ?? ((ProposalOption::where('type', $request->type)->max('sort_order') ?? 0) + 1);

        ProposalOption::create([
            'type' => $request->type,
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'icon' => $request->icon,
            'sort_order' => $order,
            'is_active' => true,
        ]);

        return back()->with('success', __(ucfirst($request->type) . ' option added successfully.'));
    }

    /**
     * Update an existing proposal option.
     */
    public function updateOption(Request $request, ProposalOption $option)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $option->update([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? $option->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __(ucfirst($option->type) . ' option updated successfully.'));
    }

    /**
     * Delete a proposal option.
     */
    public function destroyOption(ProposalOption $option)
    {
        $type = ucfirst($option->type);
        $option->delete();

        return back()->with('success', __("{$type} option deleted successfully."));
    }

    /**
     * Toggle option active/inactive status.
     */
    public function toggleOptionActive(ProposalOption $option)
    {
        $option->is_active = !$option->is_active;
        $option->save();

        return back()->with('success', __('Option status updated successfully.'));
    }
}

