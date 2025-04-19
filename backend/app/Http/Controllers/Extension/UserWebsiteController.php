<?php

namespace App\Http\Controllers\Extension;

use App\Http\Requests\StoreWebsiteRequest;
use App\Services\UserWebsiteService;
use App\Services\WebsiteService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class UserWebsiteController
{
    public function index(): JsonResponse
    {
        try {
            $websiteList = UserWebsiteService::getAllWebsiteUser();
            return response()->json($websiteList, 200);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function store(StoreWebsiteRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $websiteCount = UserWebsiteService::countWebsiteUser();
            if ($websiteCount >= 20) {
                return response()->json(['error' => 'You have reached the maximum number of websites.'], 400);
            }

            $websiteInfo = $this->fetchFavicon($validated['website_url']);
            $websiteInfo['website_name'] = ucfirst($validated['title']);

            if (isset($websiteInfo['error'])) {
                return response()->json(['error' => $websiteInfo['error']], 400);
            }

            if (!$websiteInfo['favicon']) {
                return response()->json(['error' => 'Unable to retrieve the favicon from the page.'], 400);
            }

            $website = WebsiteService::findOrCreate($websiteInfo);
            UserWebsiteService::findOrCreate(['website_id' => $website->id]);

            $websiteList = UserWebsiteService::getAllWebsiteUser();
            return response()->json($websiteList, 201);
        } catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    public function destroy($id): void
    {
        try {
            UserWebsiteService::delete($id);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            abort(500, $e->getMessage());
        }
    }

    private function fetchFavicon($url): array
    {
        try {
            $favicon = Http::get('https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=' . $url . '&size=16')->headers()['Content-Location'][0];

            return [
                'website_url' => $url,
                'favicon'     => $favicon,
            ];
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            return [
                'website_url' => $url,
                'error'       => $e->getMessage(),
            ];
        }
    }
}
