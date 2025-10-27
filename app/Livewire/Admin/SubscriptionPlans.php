<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class SubscriptionPlans extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $planId;
    
    // Form fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price = '';
    public $billing_cycle = 'monthly';
    public $trial_days = '';
    public $features = [];
    public $status = 'active';
    public $is_featured = false;
    public $sort_order = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:subscription_plans,slug',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'billing_cycle' => 'required|in:monthly,quarterly,annually',
        'trial_days' => 'nullable|integer|min:0',
        'features' => 'nullable|array',
        'status' => 'required|in:active,inactive',
        'is_featured' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected $listeners = [
        'deleteSubscriptionPlan',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function showCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function hideModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function showEditModal($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->billing_cycle = $plan->billing_cycle;
        $this->trial_days = $plan->trial_days;
        $this->features = $plan->features ?? [];
        $this->status = $plan->status;
        $this->is_featured = $plan->is_featured;
        $this->sort_order = $plan->sort_order;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function toggleStatus($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->update(['status' => $plan->status === 'active' ? 'inactive' : 'active']);
        
        session()->flash('success', 'Status erfolgreich aktualisiert!');
    }

    public function toggleFeatured($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->update(['is_featured' => !$plan->is_featured]);
        
        session()->flash('success', 'Hervorgehobener Status erfolgreich aktualisiert!');
    }

    public function deletePlan($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->delete();
        
        session()->flash('success', 'Abonnementplan erfolgreich gelöscht!');
    }

    public function save()
    {
        $rules = $this->rules;
        
        if ($this->editMode) {
            $rules['slug'] = 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId;
        }
        
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'trial_days' => $this->trial_days,
            'features' => array_filter($this->features),
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editMode) {
            SubscriptionPlan::findOrFail($this->planId)->update($data);
            session()->flash('success', 'Abonnementplan erfolgreich aktualisiert!');
        } else {
            SubscriptionPlan::create($data);
            session()->flash('success', 'Abonnementplan erfolgreich erstellt!');
        }

        $this->hideModal();
    }

    public function confirmDelete($planId)
    {
        $this->dispatch('confirm-delete', [
            'type' => 'subscription-plan',
            'id' => $planId,
            'title' => 'Abonnementplan löschen',
            'message' => 'Sind Sie sicher, dass Sie diesen Abonnementplan löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
        ]);
    }

    public function deleteSubscriptionPlan($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->delete();
        
        session()->flash('success', 'Abonnementplan erfolgreich gelöscht!');
    }

    public function addFeature()
    {
        $this->features[] = '';
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function updateSortOrder($items)
    {
        foreach ($items as $index => $planId) {
            SubscriptionPlan::where('id', $planId)->update(['sort_order' => $index + 1]);
        }
        
        session()->flash('success', 'Sortierreihenfolge erfolgreich aktualisiert!');
    }

    protected function resetForm()
    {
        $this->planId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->price = '';
        $this->billing_cycle = 'monthly';
        $this->trial_days = '';
        $this->features = [];
        $this->status = 'active';
        $this->is_featured = false;
        $this->sort_order = '';
        
        $this->resetErrorBag();
    }

    public function render()
    {
        $subscriptionPlans = SubscriptionPlan::when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('livewire.admin.subscription-plans', [
            'subscriptionPlans' => $subscriptionPlans
        ]);
    }
}
