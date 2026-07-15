<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FeaturesController extends Controller
{
    public function index(): View
    {
        return view('marketing.placeholder', [
            'title' => 'Features',
            'description' => 'Explore every module in Algos CRM.',
            'heading' => 'Features',
            'body' => 'Full feature page content arrives in Phase 3D. Navigation, layout, and components are ready for review.',
        ]);
    }
}
