<?php

namespace App\Http\Controllers;

use App\Mail\DemoRequestReceived;
use App\Models\DemoRequest;
use App\Models\SitePage;
use App\Models\Solution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DemoRequestController extends Controller
{
    public function create(): View
    {
        return view('pages.contact', [
            'page' => SitePage::findByKey('contact'),
            'solutions' => Solution::query()->published()->ordered()->get(['title']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('website')) {
            return redirect()
                ->route('contact')
                ->with('status', 'Votre demande a bien été envoyée. Notre équipe vous répondra rapidement.');
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'event_type' => ['nullable', 'string', 'max:255'],
            'selected_solution' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
            'website' => ['nullable', 'size:0'],
        ]);

        unset($data['website']);

        $demoRequest = DemoRequest::query()->create($data);

        Mail::to(config('accessevent.contact.notification_email'))
            ->send(new DemoRequestReceived($demoRequest));

        return redirect()
            ->route('contact')
            ->with('status', 'Votre demande a bien été envoyée. Notre équipe vous répondra rapidement.');
    }
}
