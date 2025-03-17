<?php

namespace App\Services;

use App\Models\Website;

class WebsiteService
{
    public static function createWebsite(array $data)
    {
        return Website::create([
            'website_name' => $data['website_name'],
            'website_url' => $data['website_url'],
            'favicon' => $data['favicon'],
        ]);
    }
}
