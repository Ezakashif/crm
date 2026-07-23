<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\ContactRequest;
use App\Mail\Marketing\ContactInquiryMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('marketing.contact', [
            'intent' => request()->query('intent'),
            'faqs' => array_merge(
                config('marketing.home.faqs', []),
                config('marketing.pricing.faqs', []),
            ),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $inquiry = $request->safe()->only([
            'name',
            'email',
            'company',
            'phone',
            'message',
            'intent',
        ]);

        Log::info('marketing.contact.inquiry', $inquiry);

        Mail::to(config('marketing.contact.email'))
            ->send(new ContactInquiryMail($inquiry));

        return redirect()
            ->route('marketing.contact', array_filter([
                'intent' => $inquiry['intent'] ?? null,
            ]))
            ->with('status', 'Thanks—your message is on its way. We’ll get back to you shortly.');
    }
}
