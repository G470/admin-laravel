<div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th wire:click="sortBy('invoice_number')" style="cursor: pointer;">
                        Rechnungsnummer
                        @if($sortField === 'invoice_number')
                            @if($sortDirection === 'asc')
                                <i class="ti ti-arrow-up"></i>
                            @else
                                <i class="ti ti-arrow-down"></i>
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('customer_name')" style="cursor: pointer;">
                        Kunde
                        @if($sortField === 'customer_name')
                            @if($sortDirection === 'asc')
                                <i class="ti ti-arrow-up"></i>
                            @else
                                <i class="ti ti-arrow-down"></i>
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('amount')" style="cursor: pointer;">
                        Betrag
                        @if($sortField === 'amount')
                            @if($sortDirection === 'asc')
                                <i class="ti ti-arrow-up"></i>
                            @else
                                <i class="ti ti-arrow-down"></i>
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                        Status
                        @if($sortField === 'status')
                            @if($sortDirection === 'asc')
                                <i class="ti ti-arrow-up"></i>
                            @else
                                <i class="ti ti-arrow-down"></i>
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                        Datum
                        @if($sortField === 'created_at')
                            @if($sortDirection === 'asc')
                                <i class="ti ti-arrow-up"></i>
                            @else
                                <i class="ti ti-arrow-down"></i>
                            @endif
                        @endif
                    </th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                    <tr>
                        <td>{{ $bill->invoice_number }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $bill->customer_name }}</span>
                                <small class="text-muted">{{ $bill->customer_email }}</small>
                            </div>
                        </td>
                        <td>{{ number_format($bill->amount, 2, ',', '.') }} €</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $bill->status_color }}">
                                {{ $bill->status_label }}
                            </span>
                        </td>
                        <td>{{ $bill->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="d-inline-flex">
                                <a href="{{ route('admin.bills.show', $bill) }}" class="text-body">
                                    <i class="ti ti-eye mx-1"></i>
                                </a>
                                <a href="{{ route('admin.bills.edit', $bill) }}" class="text-body">
                                    <i class="ti ti-edit mx-1"></i>
                                </a>
                                <a href="{{ route('admin.bills.download', $bill) }}" class="text-body">
                                    <i class="ti ti-download mx-1"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-icon text-body"
                                    wire:click="delete({{ $bill->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Keine Rechnungen gefunden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $bills->links() }}
    </div>
</div>