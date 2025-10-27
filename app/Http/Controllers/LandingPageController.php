<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\LandingPage;

class LandingPageController extends Controller
{
    public function triggerN8nWebhook(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'city' => 'nullable|string'
        ]);

        // Call the n8n webhook
        $n8nWebhookUrl = 'https://your-n8n-instance.com/webhook/create-seo-landingpage';

        $response = Http::post($n8nWebhookUrl, $data);

        return response()->json([
            'message' => 'Webhook triggered',
            'status' => $response->status(),
            'response' => $response->json()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|unique:landing_pages,slug',
            'category' => 'nullable|string',
            'city' => 'nullable|string',
            'translations' => 'required|array',
            'image_url' => 'required|string',
            'alt_text' => 'required|string'
        ]);

        $page = LandingPage::create($data);

        return response()->json(['success' => true, 'page' => $page]);
    }

    public function show($slug)
    {
        $page = LandingPage::where('slug', $slug)->firstOrFail();
        return response()->json($page);
    }
}
