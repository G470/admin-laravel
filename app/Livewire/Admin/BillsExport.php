<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Bill;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BillsExport extends Component
{
    public $format = 'pdf';
    public $dateFrom = '';
    public $dateTo = '';
    public $status = '';

    public function export()
    {
        $bills = Bill::query()
            ->when($this->dateFrom, function ($query) {
                $query->where('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('created_at', '<=', $this->dateTo);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->get();

        $filename = 'rechnungen_' . Carbon::now()->format('Y-m-d_His') . '.' . $this->format;

        switch ($this->format) {
            case 'pdf':
                $pdf = \PDF::loadView('exports.bills-pdf', ['bills' => $bills]);
                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, $filename);
                break;

            case 'excel':
                return \Excel::download(new \App\Exports\BillsExport($bills), $filename);
                break;

            case 'csv':
                return \Excel::download(new \App\Exports\BillsExport($bills), $filename);
                break;
        }
    }

    public function render()
    {
        return view('livewire.admin.bills-export');
    }
}