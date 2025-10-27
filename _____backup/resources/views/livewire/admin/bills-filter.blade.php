<div>
    <form wire:submit.prevent="applyFilters">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="search">Suche</label>
                <input type="text" class="form-control" id="search" wire:model.debounce.300ms="search"
                    placeholder="Rechnungsnummer, Kunde...">
            </div>

            <div class="col-12">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" wire:model="status">
                    <option value="">Alle</option>
                    <option value="pending">Ausstehend</option>
                    <option value="paid">Bezahlt</option>
                    <option value="overdue">Überfällig</option>
                    <option value="cancelled">Storniert</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="dateFrom">Von</label>
                <input type="date" class="form-control" id="dateFrom" wire:model="dateFrom">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="dateTo">Bis</label>
                <input type="date" class="form-control" id="dateTo" wire:model="dateTo">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-label-secondary" wire:click="resetFilters">
                    <i class="ti ti-refresh me-1"></i> Zurücksetzen
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-filter me-1"></i> Filter anwenden
                </button>
            </div>
        </div>
    </form>
</div>