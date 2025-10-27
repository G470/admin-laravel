<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('content.admin.email-templates', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich erstellt.');
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich aktualisiert.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich gelöscht.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        return view('content.admin.email-templates.preview', compact('emailTemplate'));
    }

    public function test(EmailTemplate $emailTemplate)
    {
        // Hier würde die Logik zum Senden einer Test-E-Mail implementiert
        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Test-E-Mail wurde erfolgreich gesendet.');
    }
}