<?php

namespace Laravie\Codex\Support;

use Laravie\Codex\Contracts\Sanitizer;

trait WithSanitizer
{
    /**
     * The Sanitizer.
     *
     * @var \Laravie\Codex\Contracts\Sanitizer
     */
    protected $sanitizer;

    /**
     * Check if sanitizaer exists.
     *
     * @return bool
     */
    public function hasSanitizer()
    {
        return $this->sanitizer instanceof Sanitizer;
    }

    /**
     * Set sanitizer.
     *
     * @param  \Laravie\Codex\Contracts\Sanitizer|null  $sanitizer
     *
     * @return $this
     */
    public function setSanitizer(Sanitizer $sanitizer = null)
    {
        $this->sanitizer = $sanitizer;

        return $this;
    }

    /**
     * Get sanitizer.
     *
     * @return \Laravie\Codex\Contracts\Sanitizer|null
     */
    public function getSanitizer()
    {
        return $this->sanitizer;
    }

    /**
     * Sanitize "from" for content.
     *
     * @param  array  $content
     * @param  bool  $condition
     *
     * @return array
     */
    public function sanitizeFrom(array $content, $condition = true)
    {
        return ($this->hasSanitizer() && $condition)
                    ? $this->sanitizer->from($content)
                    : $content;
    }

    /**
     * Sanitize "to" for content.
     *
     * @param  array  $content
     * @param  bool  $condition
     *
     * @return array
     */
    public function sanitizeTo(array $content, $condition = true)
    {
        return ($this->hasSanitizer() && $condition)
                    ? $this->sanitizer->to($content)
                    : $content;
    }
}
