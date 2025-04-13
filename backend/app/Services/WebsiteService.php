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
        $url = $data['website_url'];

        $existing = Website::where('website_url', 'LIKE', $url . '%')
            ->orWhereRaw('? LIKE website_url || \'%\'', [$url])
            ->first();

        if ($existing) {
            return $existing;
        }
        return self::create($data);
    }
}
