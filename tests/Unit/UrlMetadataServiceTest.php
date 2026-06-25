<?php

namespace Tests\Unit;

use App\Services\UrlMetadataService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class UrlMetadataServiceTest extends TestCase
{
    public function test_it_parses_content_type_header_and_html_metadata(): void
    {
        $client = new Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], '<html><head><title>Example title</title><meta name="description" content="Example description"></head></html>'),
            ])),
        ]);

        $metadata = (new UrlMetadataService($client))->parse('http://example.test');

        $this->assertSame('Example title', $metadata['title']);
        $this->assertSame('Example description', $metadata['description']);
    }
}
