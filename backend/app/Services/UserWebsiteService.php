<?php

namespace App\Services;

use App\Models\UserWebsite;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class UserWebsiteService
{
    public static function create(array $data)
    {
        return UserWebsite::create([
            'user_id' => Auth::id(),
            'website_id' => $data['website_id'],
        ]);
    }

    public static function findOrCreate(array $data)
    {
        $userWebsite = UserWebsite::where('user_id', Auth::id())
            ->where('website_id', $data['website_id'])
            ->first();

        if ($userWebsite) {
            return $userWebsite;
        }

        return UserWebsite::create([
            'user_id' => Auth::id(),
            'website_id' => $data['website_id'],
        ]);
    }

    public static function getAllWebsiteUser()
    {
        return UserWebsite::where('user_id', Auth::id())->with('website')->get();
    }

    public static function delete($id)
    {
        UserWebsite::where('user_id', Auth::id())->where('id', $id)->delete();
    }
}
