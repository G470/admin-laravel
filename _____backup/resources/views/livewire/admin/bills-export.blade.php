<div>
    <form wire:submit.prevent="export">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="format">Format</label>
                <select class="form-select" id="format" wire:model="format">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                </select>
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

            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-download me-1"></i> Export starten
                </button>
            </div>
        </div>
    </form>
</div>