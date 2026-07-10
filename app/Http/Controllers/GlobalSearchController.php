<?php

namespace App\Http\Controllers;

use App\Http\Requests\GlobalSearchRequest;
use App\Services\GlobalSearchService;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function __construct(
        protected GlobalSearchService $search,
    ) {}

    public function index(GlobalSearchRequest $request): View
    {
        return view('search.index', $this->search->search(
            $request->user(),
            $request->term(),
        ));
    }
}
