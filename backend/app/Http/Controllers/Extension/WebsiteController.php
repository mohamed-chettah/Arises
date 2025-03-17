<?php

namespace App\Http\Controllers\Extension;

use App\Services\WebsiteService;
use Illuminate\Http\Request;

class WebsiteController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_url' => 'required|url',
        ]);





        $website = WebsiteService::createWebsite($validated);

        return response()->json($website, 201);
    }

}
