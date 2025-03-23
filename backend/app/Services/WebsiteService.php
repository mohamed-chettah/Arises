<?php

namespace App\Services;

use App\Models\Website;

class WebsiteService
{
    public static function create(array $data)
    {
        return Website::create([
            'website_name' => $data['website_name'],
            'website_url' => $data['website_url'],
            'favicon' => $data['favicon'],
        ]);
    }

    public static function findOrCreate(array $data)
    {
        $website = Website::where('website_url', $data['website_url'])->first();

        if ($website) {
            return $website;
        }

        return self::create($data);
    }
}
