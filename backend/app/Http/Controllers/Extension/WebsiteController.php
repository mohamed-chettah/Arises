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

    private function getFromHtml(string $html, array $tags) {

        $collect = [];

        // Turn off default error reporting so we're not drowning
        // in errors when the HTML is malformed. We can get a
        // hold of them anytime via libxml_get_errors().
        // Cf. https://www.php.net/libxml_use_internal_errors
        libxml_use_internal_errors(true);

        // Turn HTML string into a DOM tree.
        $dom = new DomDocument;
        $dom->loadHTML($html);

        // Set up XPath
        $xpath = new DomXPath($dom);

        // Query the DOM tree for the given set of tags.
        foreach ($tags as $tag) {

            // You can do *a lot* more with XPath, cf. this cheat sheet:
            // https://gist.github.com/LeCoupa/8c305ec8c713aad07b14
            $result = $xpath->query("//{$tag}");

            if ($result instanceof DOMNodeList) {

                $collect[$tag] = $result;
            }
        }

        // Clear errors to free up memory, cf.
        // https://www.php.net/manual/de/function.libxml-use-internal-errors.php#78236
        libxml_clear_errors();

        return $collect;
    }

}
