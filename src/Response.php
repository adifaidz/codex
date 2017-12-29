<?php

namespace Laravie\Codex;

use BadMethodCallException;
use Psr\Http\Message\ResponseInterface;

class Response implements Contracts\Response
{
    use Support\WithSanitizer;

    /**
     * The original response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $original;

    /**
     * Construct a new response.
     *
     * @param \Psr\Http\Message\ResponseInterface  $original
     */
    public function __construct(ResponseInterface $original)
    {
        $this->original = $original;
    }

    /**
     * Validate the response object.
     *
     * @return $this
     */
    public function validate(): self
    {
        return $this;
    }

    /**
     * Validate response with custom callable.
     *
     * @param  callable  $callback
     *
     * @return $this
     */
    public function validateWith(callable $callback): self
    {
        call_user_func($callback, $this->getStatusCode(), $this);

        return $this;
    }

    /**
     * Convert response body to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->sanitizeTo($this->getContent());
    }

    /**
     * Get body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->original->getBody();
    }

    /**
     * Get content from body, by default we assume it returning JSON.
     *
     * @return mixed
     */
    public function getContent()
    {
        return json_decode($this->getBody(), true);
    }

    /**
     * Get status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->original->getStatusCode();
    }

    /**
     * Call method under \Psr\Http\Message\ResponseInterface.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (! method_exists($this->original, $method)) {
            throw new BadMethodCallException("Method [{$method}] doesn't exists.");
        }

        return $this->original->{$method}(...$parameters);
    }
}
