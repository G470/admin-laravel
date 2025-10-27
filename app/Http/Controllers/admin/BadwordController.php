<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badword;
use Illuminate\Http\Request;

class BadwordController extends Controller
{
    public function index()
    {
        // With Livewire, we no longer need to pass badwords to the view
        return view('content.admin.badwords');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:255|unique:badwords,word',
            'replacement' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Badword::create($validated);

        return redirect()->route('admin.badwords.index')
            ->with('success', 'Badword wurde erfolgreich erstellt.');
    }

    public function update(Request $request, Badword $badword)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:255|unique:badwords,word,' . $badword->id,
            'replacement' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $badword->update($validated);

        return redirect()->route('admin.badwords.index')
            ->with('success', 'Badword wurde erfolgreich aktualisiert.');
    }

    public function destroy(Badword $badword)
    {
        $badword->delete();

        return redirect()->route('admin.badwords.index')
            ->with('success', 'Badword wurde erfolgreich gelöscht.');
    }
}
