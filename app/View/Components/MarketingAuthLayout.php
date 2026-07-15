<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MarketingAuthLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $heading = null,
        public ?string $subheading = null,
    ) {}

    public function render(): View
    {
        return view('layouts.marketing-auth');
    }
}
