<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Header;

class UrlMetadataService
{
    /**
     * Inject the HTTP client used for URL metadata lookups.
     */
    public function __construct(private readonly ?HttpClient $client = null)
    {
    }

    /**
     * Fetch a URL and extract metadata from its response.
     *
     * @return array<string, string>
     */
    public function parse(string $url): array
    {
        if (!in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return [];
        }

        try {
            $response = ($this->client ?? new HttpClient())->request('GET', $url, [
                'timeout' => 5,
                'http_errors' => false,
                'allow_redirects' => ['max' => 3],
            ]);

            $headers = Header::parse($response->getHeader('content-type'));
            $charset = $headers[0]['charset'] ?? 'UTF-8';

            return $this->formatMetaTags(mb_convert_encoding((string) $response->getBody(), 'UTF-8', $charset));
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Extract title and meta tags from raw HTML.
     *
     * @return array<string, string>
     */
    public function formatMetaTags(string $html): array
    {
        $meta = [];
        $pattern = '~<\s*meta\s(?=[^>]*?\b(?:name|property|http-equiv)\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=)))[^>]*?\bcontent\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))[^>]*>~ix';

        if (preg_match_all($pattern, $html, $matches)) {
            $meta = array_combine(array_map('strtolower', $matches[1]), $matches[2]) ?: [];
        }

        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $title)) {
            $meta['title'] = html_entity_decode(trim($title[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $meta;
    }
}
