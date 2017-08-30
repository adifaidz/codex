<?php

namespace Laravie\Codex;

class Sanitizer implements Contracts\Sanitizer
{
    /**
     * Sanitization rules.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Add sanitization rules.
     *
     * @param  string|array  $group
     * @param  \Laravie\Codex\Contracts\Cast  $cast
     *
     * @return $this
     */
    public function add($group, Contracts\Cast $cast)
    {
        $this->casts = \igorw\assoc_in($this->casts, (array) $group, $cast);

        return $this;
    }

    /**
     * Sanitize request.
     *
     * @param  array  $inputs
     * @param  array  $group
     *
     * @return array
     */
    public function from(array $inputs, array $group = [])
    {
        $data = [];

        foreach ($inputs as $name => $input) {
            $data[$name] = $this->sanitizeFrom($input, $name, $group);
        }

        return $data;
    }

    /**
     * Sanitize response.
     *
     * @param  array  $inputs
     * @param  array  $group
     *
     * @return array
     */
    public function to(array $inputs, $group = [])
    {
        $data = [];

        foreach ($inputs as $name => $input) {
            $data[$name] = $this->sanitizeTo($input, $name, $group);
        }

        return $data;
    }

    /**
     * Sanitize request from.
     *
     * @param  mixed  $value
     * @param  string  $name
     * @param  array  $group
     *
     * @return mixed
     */
    protected function sanitizeFrom($value, $name, array $group = [])
    {
        array_push($group, $name);

        if (is_array($value)) {
            return $this->from($value, $group);
        }

        return ! is_null($caster = $this->getCaster($group))
                    ? $caster->from($value)
                    : $value;
    }

    /**
     * Sanitize response to.
     *
     * @param  mixed  $value
     * @param  string  $name
     * @param  array  $group
     *
     * @return mixed
     */
    protected function sanitizeTo($value, $name, array $group = [])
    {
        array_push($group, $name);

        if (is_array($value)) {
            return $this->to($value, $group);
        }

        return ! is_null($caster = $this->getCaster($group))
                    ? $caster->to($value)
                    : $value;
    }

    /**
     * Get caster.
     *
     * @param  string|array  $group
     *
     * @return \Laravie\Codex\Contracts\Cast
     */
    protected function getCaster($group)
    {
        return \igorw\get_in($this->casts, (array) $group);
    }
}
