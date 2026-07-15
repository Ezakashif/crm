<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('marketing.placeholder', [
            'title' => 'About',
            'description' => 'Why we built Algos CRM.',
            'heading' => 'About',
            'body' => 'Full about page content arrives in Phase 3G.',
        ]);
    }
}
