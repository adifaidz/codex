<?php

namespace Laravie\Codex\TestCase;

use Laravie\Codex\Endpoint;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    /** @test */
    function it_can_build_basic_endpoint()
    {
        $endpoint = new Endpoint('https://laravel.com', 'docs');

        $this->assertSame('https://laravel.com', $endpoint->getUri());
        $this->assertSame(['docs'], $endpoint->getPath());
        $this->assertSame([], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('https://laravel.com/docs', (string) $endpoint->get());
    }

    /** @test */
    function it_can_build_basic_endpoint_with_tailing_slash()
    {
        $endpoint = new Endpoint('https://laravel.com/', 'docs');

        $this->assertSame('https://laravel.com', $endpoint->getUri());
        $this->assertSame(['docs'], $endpoint->getPath());
        $this->assertSame([], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('https://laravel.com/docs', (string) $endpoint->get());
    }

    /** @test */
    function it_can_build_endpoint_with_multiple_paths()
    {
        $endpoint = new Endpoint('https://laravel.com', ['docs', '5.4']);

        $this->assertSame('https://laravel.com', $endpoint->getUri());
        $this->assertSame(['docs', '5.4'], $endpoint->getPath());
        $this->assertSame([], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('https://laravel.com/docs/5.4', (string) $endpoint->get());
    }

    /** @test */
    function it_can_build_basic_endpoint_with_query_string()
    {
        $endpoint = new Endpoint('https://laravel.com', 'docs', ['search' => 'controller']);

        $this->assertSame('https://laravel.com', $endpoint->getUri());
        $this->assertSame(['docs'], $endpoint->getPath());
        $this->assertSame(['search' => 'controller'], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('https://laravel.com/docs?search=controller', (string) $endpoint->get());
    }

    /** @test */
    function it_can_build_basic_endpoint_appended_query_string()
    {
        $endpoint = (new Endpoint('https://laravel.com', 'docs'))
                        ->addQuery(['search' => 'controller', 'page' => 3]);

        $this->assertSame('https://laravel.com', $endpoint->getUri());
        $this->assertSame(['docs'], $endpoint->getPath());
        $this->assertSame(['search' => 'controller', 'page' => 3], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('https://laravel.com/docs?search=controller&page=3', (string) $endpoint->get());
    }

    /** @test */
    function it_can_set_endpoint_with_no_uri()
    {
        $endpoint = new Endpoint(null, 'docs');

        $this->assertSame(null, $endpoint->getUri());
        $this->assertSame(['docs'], $endpoint->getPath());
        $this->assertSame([], $endpoint->getQuery());

        $this->assertInstanceOf('GuzzleHttp\Psr7\Uri', $endpoint->get());
        $this->assertSame('/docs', (string) $endpoint->get());
    }
}
