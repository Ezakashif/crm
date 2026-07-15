<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        return view('marketing.placeholder', [
            'title' => 'Pricing',
            'description' => 'Simple plans for growing teams.',
            'heading' => 'Pricing',
            'body' => 'Full pricing page content arrives in Phase 3E. Pricing cards are available in the component library on the home foundation page.',
        ]);
    }
}
