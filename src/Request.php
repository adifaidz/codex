<?php

namespace Laravie\Codex;

use Laravie\Codex\Contracts\Sanitizable;
use Laravie\Codex\Support\WithSanitizer;
use Laravie\Codex\Contracts\Request as RequestContract;

abstract class Request implements RequestContract
{
    use WithSanitizer;

    /**
     * Version namespace.
     *
     * @var string
     */
    protected $version;

    /**
     * The Codex client.
     *
     * @var \Laravie\Codex\Client
     */
    protected $client;

    /**
     * Construct a new Collection.
     *
     * @param \Laravie\Codex\Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        if ($this instanceof Sanitizable) {
            $this->setSanitizer($this->sanitizeWith());
        }
    }

    /**
     * Get API version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Send API request.
     *
     * @param  string  $method
     * @param  string  $path
     * @param  array  $headers
     * @param  \Psr\Http\Message\StreamInterface|array|null  $body
     *
     * @return \Laravie\Codex\Reponse
     */
    protected function send($method, $path, array $headers = [], $body = [])
    {
        if ($this->hasSanitizer() && is_array($body)) {
            $body = $this->getSanitizer()->from($body);
        }

        $endpoint = $this->getApiEndpoint($path);

        if (strtoupper($method) === 'GET') {
            $endpoint->addQuery($body);
        }

        return $this->client->send($method, $this->resolveUri($endpoint), $headers, $body)
                    ->setSanitizer($this->getSanitizer());
    }

    /**
     * Get API Header.
     *
     * @return array
     */
    protected function getApiHeaders()
    {
        return [];
    }

    /**
     * Get API Body.
     *
     * @return array
     */
    protected function getApiBody()
    {
        return [];
    }

    /**
     * Merge API Headers.
     *
     * @param  array  $headers
     *
     * @return array
     */
    protected function mergeApiHeaders(array $headers = [])
    {
        return array_merge($this->getApiHeaders(), $headers);
    }

    /**
     * Merge API Body.
     *
     * @param  array  $headers
     *
     * @return array
     */
    protected function mergeApiBody(array $body = [])
    {
        return array_merge($this->getApiBody(), $body);
    }

    /**
     * Get API Endpoint.
     *
     * @param  string|array  $path
     *
     * @return \Laravie\Codex\Endpoint
     */
    protected function getApiEndpoint($path = [])
    {
        return new Endpoint($this->client->getApiEndpoint(), $path);
    }

    /**
     * Resolve URI.
     *
     * @param  \Laravie\Codex\Endpoint  $endpoint
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    protected function resolveUri(Endpoint $endpoint)
    {
        return $endpoint->get();
    }
}
