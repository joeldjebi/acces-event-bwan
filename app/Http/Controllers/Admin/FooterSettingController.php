<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.footer', [
            'settings' => SiteSetting::footerData(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer_tagline' => ['nullable', 'string', 'max:500'],
            'footer_address' => ['nullable', 'string', 'max:500'],
            'footer_phone' => ['nullable', 'string', 'max:100'],
            'footer_email' => ['nullable', 'email', 'max:255'],
            'footer_hours' => ['nullable', 'string', 'max:255'],
            'footer_whatsapp' => ['nullable', 'string', 'max:100'],
            'footer_linkedin_url' => ['nullable', 'string', 'max:255'],
            'footer_facebook_url' => ['nullable', 'string', 'max:255'],
            'footer_legal_text' => ['nullable', 'string', 'max:500'],
        ]);

        SiteSetting::updateFooter($data);

        return redirect()
            ->route('admin.settings.footer.edit')
            ->with('status', 'Pied de page mis à jour.');
    }
}
