<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AdminCreditGrant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreditGrantsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $vendorFilter = '';
    public $adminFilter = '';
    public $grantTypeFilter = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'vendorFilter' => ['except' => ''],
        'adminFilter' => ['except' => ''],
        'grantTypeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingVendorFilter()
    {
        $this->resetPage();
    }

    public function updatingAdminFilter()
    {
        $this->resetPage();
    }

    public function updatingGrantTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'vendorFilter',
            'adminFilter',
            'grantTypeFilter',
            'statusFilter',
            'dateFrom',
            'dateTo'
        ]);
        $this->resetPage();
    }

    public function deleteGrant($grantId)
    {
        $grant = AdminCreditGrant::findOrFail($grantId);

        if (!$grant->canBeDeleted()) {
            session()->flash('error', 'Diese Credit-Vergabe kann nicht gelöscht werden.');
            return;
        }

        try {
            $grant->delete();
            session()->flash('success', 'Credit-Vergabe wurde erfolgreich gelöscht.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen der Credit-Vergabe.');
        }
    }

    public function getCreditGrantsProperty()
    {
        $query = AdminCreditGrant::with(['admin', 'vendor', 'creditPackage']);

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('vendor', function ($vendorQuery) {
                    $vendorQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('admin', function ($adminQuery) {
                        $adminQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->vendorFilter) {
            $query->where('vendor_id', $this->vendorFilter);
        }

        if ($this->adminFilter) {
            $query->where('admin_id', $this->adminFilter);
        }

        if ($this->grantTypeFilter) {
            $query->where('grant_type', $this->grantTypeFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('grant_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('grant_date', '<=', $this->dateTo);
        }

        // Apply sorting
        switch ($this->sortField) {
            case 'vendor':
                $query->join('users as vendor_users', 'admin_credit_grants.vendor_id', '=', 'vendor_users.id')
                    ->orderBy('vendor_users.name', $this->sortDirection)
                    ->select('admin_credit_grants.*');
                break;
            case 'admin':
                $query->join('users as admin_users', 'admin_credit_grants.admin_id', '=', 'admin_users.id')
                    ->orderBy('admin_users.name', $this->sortDirection)
                    ->select('admin_credit_grants.*');
                break;
            case 'grant_type':
                $query->orderBy('grant_type', $this->sortDirection);
                break;
            case 'credits_granted':
                $query->orderBy('credits_granted', $this->sortDirection);
                break;
            case 'grant_date':
                $query->orderBy('grant_date', $this->sortDirection);
                break;
            default:
                $query->orderBy($this->sortField, $this->sortDirection);
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getVendorsProperty()
    {
        return User::where('is_vendor', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function getAdminsProperty()
    {
        return User::where('is_admin', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function getGrantTypeOptionsProperty()
    {
        return AdminCreditGrant::getGrantTypeOptions();
    }

    public function getStatusOptionsProperty()
    {
        return AdminCreditGrant::getStatusOptions();
    }

    public function getStatisticsProperty()
    {
        return AdminCreditGrant::getStatistics();
    }

    public function render()
    {
        return view('livewire.admin.credit-grants-table');
    }
}