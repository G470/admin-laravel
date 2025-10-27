<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index()
    {
        return view('content.admin.bills');
    }

    public function show(Bill $bill)
    {
        return view('content.admin.bills.show', compact('bill'));
    }

    public function create()
    {
        return view('content.admin.bills.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['invoice_number'] = 'INV-' . strtoupper(Str::random(8));
        $validated['status'] = 'pending';

        $bill = Bill::create($validated);

        return redirect()->route('admin.bills.show', $bill)
            ->with('success', 'Rechnung wurde erfolgreich erstellt.');
    }

    public function edit(Bill $bill)
    {
        return view('content.admin.bills.edit', compact('bill'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'paid' && $bill->status !== 'paid') {
            $validated['paid_at'] = now();
        }

        $bill->update($validated);

        return redirect()->route('admin.bills.show', $bill)
            ->with('success', 'Rechnung wurde erfolgreich aktualisiert.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->route('admin.bills.index')
            ->with('success', 'Rechnung wurde erfolgreich gelöscht.');
    }

    public function download(Bill $bill)
    {
        $pdf = Pdf::loadView('exports.bill-pdf', ['bill' => $bill]);
        return $pdf->download('rechnung_' . $bill->invoice_number . '.pdf');
    }
}