<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.admin.subscription-plans');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.admin.subscription-plans-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = array_filter($validated['features']);
        }

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Abonnementplan wurde erfolgreich erstellt.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('content.admin.subscription-plans-edit', compact('subscriptionPlan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = array_filter($validated['features']);
        }

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Abonnementplan wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Abonnementplan wurde erfolgreich gelöscht.');
    }

    /**
     * Update the order of subscription plans
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:subscription_plans,id',
        ]);

        foreach ($request->items as $index => $id) {
            SubscriptionPlan::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
