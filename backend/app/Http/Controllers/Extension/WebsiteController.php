<?php

namespace App\Http\Controllers\Extension;

use App\Services\WebsiteService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebsiteController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_url' => 'required|url',
        ]);

        $websiteInfo = $this->fetchTitleAndFavicon($validated['website_url']);

        dd($websiteInfo);
        $website = WebsiteService::createWebsite($validated);

        return response()->json($website, 201);
    }

    private function fetchTitleAndFavicon($url): array
    {
        try {
            // Récupération du favicon
            $favicon = Http::get('https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=' . $url . '&size=16')->headers()['Content-Location'][0];

            // Récupération du HTML de la page
            $response = Http::get($url);
            $html = $response->body();

            if (empty($html)) {
                return [
                    'url'     => $url,
                    'title'   => null,
                    'favicon' => $favicon,
                    'error'   => 'Le HTML est vide.',
                ];
            }

            // Charger le HTML dans DOMDocument
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            // Utiliser DOMXPath pour récupérer le titre
            $xpath = new DOMXPath($dom);
            $titleNodes = $xpath->query('//title');

            $title = $titleNodes->length > 0 ? trim($titleNodes->item(0)->nodeValue) : null;

            return [
                'url'     => $url,
                'title'   => $title,
                'favicon' => $favicon,
            ];
        } catch (\Exception $e) {
            return [
                'url'     => $url,
                'title'   => null,
                'favicon' => null,
                'error'   => $e->getMessage(),
            ];
        }
    }

}
