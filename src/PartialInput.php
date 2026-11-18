<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use stdClass;

final class PartialInput
{
    private string $raw;

    private string $reference;

    private stdClass $arguments;

    final public function __construct(
        private readonly string $fullReference
    ) {
    }

    public function raw(): string
    {
        if (isset($this->raw) === false) {
            $this->raw = trim($this->fullReference);
        }
        return $this->raw;
    }

    public function reference(): string
    {
        if (isset($this->reference) === false) {
            $r = $this->raw();
            if (str_contains($r, ':')) {
                list ($r, $ignore) = explode(':', $r, 2);
            }

            $this->reference = $r;
        }
        return $this->reference;
    }

    public function arguments(): stdClass
    {
        if (isset($this->arguments) === false) {
            $r = $this->raw();
            if (str_contains($r, ':') === false) {
                return new stdClass();
            }

            list($ignore, $args) = explode(':', $r, 2);
            $argList = explode(',', $args);

            $build = new stdClass();
            foreach ($argList as $arg) {
                list($key, $value) = explode('=', $arg);
                $key   = trim($key);
                $value = trim($value);
                $build->{$key} = $value;
            }

            $this->arguments = $build;
        }
        return $this->arguments;
    }
}
