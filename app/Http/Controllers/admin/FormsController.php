<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;

class FormsController extends Controller
{
    public function index()
    {
        $forms = Form::all();
        return view('content.admin.forms', compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:255',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array'
        ]);

        $form = Form::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'fields' => json_encode($validated['fields']),
            'status' => 'active'
        ]);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich erstellt.');
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:255',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array'
        ]);

        $form->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'fields' => json_encode($validated['fields'])
        ]);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich aktualisiert.');
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich gelöscht.');
    }

    public function toggleStatus(Form $form)
    {
        $form->status = $form->status === 'active' ? 'inactive' : 'active';
        $form->save();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formularstatus wurde erfolgreich aktualisiert.');
    }
}