<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::withCount('submissions')->get();
        return view('content.admin.forms', compact('forms'));
    }

    public function create()
    {
        return view('content.admin.forms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:50',
            'fields.*.required' => 'boolean',
            'status' => 'required|in:active,inactive',
            'success_message' => 'nullable|string',
            'error_message' => 'nullable|string',
            'email_notification' => 'boolean',
            'notification_emails' => 'nullable|array',
            'notification_emails.*' => 'email',
            'redirect_url' => 'nullable|url',
        ]);

        Form::create($validated);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich erstellt.');
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:50',
            'fields.*.required' => 'boolean',
            'status' => 'required|in:active,inactive',
            'success_message' => 'nullable|string',
            'error_message' => 'nullable|string',
            'email_notification' => 'boolean',
            'notification_emails' => 'nullable|array',
            'notification_emails.*' => 'email',
            'redirect_url' => 'nullable|url',
        ]);

        $form->update($validated);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich aktualisiert.');
    }

    public function edit(Form $form)
    {
        return view('content.admin.forms.edit', compact('form'));
    }

    public function destroy(Form $form)
    {
        if ($form->submissions()->exists()) {
            return redirect()->route('admin.forms.index')
                ->with('error', 'Dieses Formular kann nicht gelöscht werden, da es noch Einreichungen enthält.');
        }

        $form->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Formular wurde erfolgreich gelöscht.');
    }

    public function toggleStatus(Form $form)
    {
        $form->update([
            'status' => $form->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Status wurde erfolgreich aktualisiert.');
    }

    public function submissions(Form $form)
    {
        $submissions = $form->submissions()->latest()->paginate(20);
        return view('content.admin.forms.submissions', compact('form', 'submissions'));
    }
}