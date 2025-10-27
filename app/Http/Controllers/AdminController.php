<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminCategory;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        return view('content.admin.dashboard');
    }

    // Benutzerverwaltung
    public function users()
    {
        return view('content.admin.users');
    }

    // Alle Vermietungsobjekte
    public function rentals()
    {
        return view('content.admin.rentals');
    }

    // Kategorienverwaltung
    public function categories()
    {
        $categories = AdminCategory::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        return view('content.admin.categories', compact('categories'));
    }

    // SEO für Städte
    public function citiesSeo()
    {
        return view('content.admin.cities-seo');
    }

    // Formularverwaltung
    public function forms()
    {
        return view('content.admin.forms');
    }

    // E-Mail-Vorlagen
    public function emailTemplates()
    {
        return view('content.admin.email-templates');
    }

    // Homepage Einstellungen
    public function homepage()
    {
        return view('content.admin.homepage');
    }

    // Systemeinstellungen
    public function settings()
    {
        return view('content.admin.settings');
    }

    // Sperrlisten für Wörter
    public function badwords()
    {
        return view('content.admin.badwords');
    }

    // Rechnungsverwaltung
    public function bills()
    {
        return view('content.admin.bills');
    }
}