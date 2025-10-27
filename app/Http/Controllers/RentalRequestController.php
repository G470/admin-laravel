<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RentalRequestController extends Controller
{
    /**
     * Configuration for Vuexy layout
     *
     * @return array
     */
    private function getPageConfigs()
    {
        return [
            'bodyClass' => 'rental-request-page',
            'navbarType' => 'fixed',
            'footerFixed' => false,
            'pageHeader' => false,
            'defaultLayout' => 'front'
        ];
    }

    /**
     * Show the rental request form
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $rental = Rental::with(['vendor', 'category', 'location'])->findOrFail($id);
        $pageConfigs = $this->getPageConfigs();

        return view('inlando.rental-request', compact('rental', 'pageConfigs'));
    }

    /**
     * Store the rental request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $id)
    {
        $rental = Rental::with('vendor')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after:date_from',
            'rental_type' => 'required|in:hourly,daily,once',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Bitte geben Sie Ihren Namen ein.',
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'message.required' => 'Bitte geben Sie eine Nachricht ein.',
            'date_from.required' => 'Bitte wählen Sie ein Start-Datum.',
            'date_from.after_or_equal' => 'Das Start-Datum darf nicht in der Vergangenheit liegen.',
            'date_to.required' => 'Bitte wählen Sie ein End-Datum.',
            'date_to.after' => 'Das End-Datum muss nach dem Start-Datum liegen.',
            'rental_type.required' => 'Bitte wählen Sie eine Mietart.',
            'terms.required' => 'Bitte akzeptieren Sie die Nutzungsbedingungen.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate estimated price
        $estimatedPrice = $this->calculateEstimatedPrice($rental, $request);

        // Prepare email data
        $emailData = [
            'rental' => $rental,
            'requester' => [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ],
            'request_details' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'rental_type' => $request->rental_type,
                'message' => $request->message,
                'estimated_price' => $estimatedPrice,
            ]
        ];

        // Send email to vendor
        try {
            Mail::send('emails.rental-request', $emailData, function ($message) use ($rental, $request) {
                $message->to($rental->vendor->email, $rental->vendor->name)
                    ->subject('Neue Mietanfrage für: ' . $rental->title)
                    ->replyTo($request->email, $request->name);
            });

            // Send confirmation email to requester
            Mail::send('emails.rental-request-confirmation', $emailData, function ($message) use ($request) {
                $message->to($request->email, $request->name)
                    ->subject('Ihre Mietanfrage wurde gesendet');
            });

            return redirect()->route('rental.request', $id)
                ->with('success', 'Ihre Anfrage wurde erfolgreich gesendet! Der Vermieter wird sich in Kürze bei Ihnen melden.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Es gab ein Problem beim Senden Ihrer Anfrage. Bitte versuchen Sie es später erneut.')
                ->withInput();
        }
    }

    /**
     * Calculate estimated price based on rental type and duration
     *
     * @param Rental $rental
     * @param Request $request
     * @return float
     */
    private function calculateEstimatedPrice($rental, $request)
    {
        $dateFrom = new \DateTime($request->date_from);
        $dateTo = new \DateTime($request->date_to);
        $interval = $dateFrom->diff($dateTo);

        switch ($request->rental_type) {
            case 'hourly':
                // Assume 8 hours per day for hourly calculation
                $hours = ($interval->days * 24) + $interval->h;
                return $hours * $rental->price_range_hour;

            case 'daily':
                $days = max(1, $interval->days);
                return $days * $rental->price_range_day;

            case 'once':
                return $rental->price_range_once;

            default:
                return $rental->price_range_day;
        }
    }
}
