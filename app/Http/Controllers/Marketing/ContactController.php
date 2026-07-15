<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('marketing.placeholder', [
            'title' => 'Contact',
            'description' => 'Talk with the Algos team.',
            'heading' => 'Contact',
            'body' => 'Full contact page and form arrive in Phase 3F.',
        ]);
    }
}
