<?php

namespace Laravie\Codex\Support;

use GuzzleHttp\Psr7\Uri;
use Laravie\Codex\Payload;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Laravie\Codex\Contracts\Endpoint as EndpointContract;
use Laravie\Codex\Contracts\Response as ResponseContract;

trait HttpClient
{
    /**
     * Http Client instance.
     *
     * @var \Http\Client\Common\HttpMethodsClient
     */
    protected $http;

    /**
     * List of HTTP requests.
     *
     * @var array
     */
    protected $httpRequestQueries = [];

    /**
     * Send the HTTP request.
     *
     * @param  string  $method
     * @param  \Laravie\Codex\Contracts\Endpoint  $uri
     * @param  array  $headers
     * @param  \Psr\Http\Message\StreamInterface|\Laravie\Codex\Payload|array|null  $body
     *
     * @return \Laravie\Codex\Contracts\Response
     */
    public function send(string $method, EndpointContract $uri, array $headers = [], $body = []): ResponseContract
    {
        $method = strtoupper($method);

        if ($method === 'GET' && ! $body instanceof StreamInterface) {
            $uri->addQuery($body);
            $body = null;
        }

        list($headers, $body) = $this->prepareRequestPayloads($headers, $body);

        return $this->requestWith($method, $uri->get(), $headers, $body);
    }

    /**
     * Stream (multipart) the HTTP request.
     *
     * @param  string  $method
     * @param  \Laravie\Codex\Contracts\Endpoint  $uri
     * @param  array  $headers
     * @param  \Psr\Http\Message\StreamInterface  $stream
     *
     * @return \Laravie\Codex\Contracts\Response
     */
    public function stream(string $method, EndpointContract $uri, array $headers = [], StreamInterface $stream): ResponseContract
    {
        list($headers, $stream) = $this->prepareRequestPayloads($headers, $stream);

        return $this->requestWith(strtoupper($method), $uri->get(), $headers, $stream);
    }

    /**
     * Stream (multipart) the HTTP request.
     *
     * @param  string  $method
     * @param  \Psr\Http\Message\UriInterface  $uri
     * @param  array  $headers
     * @param  \Psr\Http\Message\StreamInterface|\Laravie\Codex\Payload|array|null  $body
     *
     * @return \Laravie\Codex\Contracts\Response
     */
    protected function requestWith(string $method, UriInterface $uri, array $headers, $body): ResponseContract
    {
        if (in_array($method, ['HEAD', 'GET', 'TRACE'])) {
            $body = null;
        }

        $response = $this->responseWith(
            $this->http->send($method, $uri, $headers, $body)
        );

        $this->httpRequestQueries[] = compact('method', 'uri', 'headers', 'body', 'response');

        return $response;
    }

    /**
     * Prepare request payloads.
     *
     * @param  array  $headers
     * @param  \Psr\Http\Message\StreamInterface|\Laravie\Codex\Payload|array|null  $body
     *
     * @return array
     */
    protected function prepareRequestPayloads(array $headers = [], $body = []): array
    {
        $headers = $this->prepareRequestHeaders($headers);

        return [$headers, Payload::make($body)->get($headers)];
    }

    /**
     * Resolve the responder class.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     *
     * @return \Laravie\Codex\Contracts\Response
     */
    abstract protected function responseWith(ResponseInterface $response): ResponseContract;

    /**
     * Prepare request headers.
     *
     * @param  array  $headers
     *
     * @return array
     */
    abstract protected function prepareRequestHeaders(array $headers = []): array;
}
