<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplatesController extends Controller
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
            'content' => 'required|string'
        ]);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich erstellt.');
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $template->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich aktualisiert.');
    }

    public function destroy(EmailTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'E-Mail-Vorlage wurde erfolgreich gelöscht.');
    }
}