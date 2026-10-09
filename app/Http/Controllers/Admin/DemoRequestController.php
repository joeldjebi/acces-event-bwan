<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.demo-requests.index', [
            'demoRequests' => DemoRequest::query()
                ->latest()
                ->paginate(15),
            'statuses' => DemoRequest::STATUSES,
        ]);
    }

    public function show(DemoRequest $demoRequest): View
    {
        return view('admin.demo-requests.show', [
            'demoRequest' => $demoRequest,
            'statuses' => DemoRequest::STATUSES,
        ]);
    }

    public function update(Request $request, DemoRequest $demoRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(DemoRequest::STATUSES))],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $data['handled_at'] = in_array($data['status'], ['handled', 'archived'], true)
            ? now()
            : null;

        $demoRequest->update($data);

        return redirect()
            ->route('admin.demo-requests.show', $demoRequest)
            ->with('status', 'Demande mise à jour.');
    }
}
