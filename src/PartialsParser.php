<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Parser\MarkdownParserInterface;

use StdClass;

use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Parser\Cursor;

use Eightfold\CommonMarkPartials\PartialInterface;

final class PartialsParser
{
    private const REGEX_PATTERN = '{!!([\S\s]*?)!!}';

    private array $partialsList = [];

    final public function __construct(private readonly array $config)
    {
        $this->partialsList = $config['partials'];
    }

    public function parse(string $input): MarkdownInput
    {
        $matches = $this->matchesInContent($input);
        if (count($matches->to_replace) === 0) {
            return new MarkdownInput($input);
        }

        $replacements = [];
        $toReplace    = $matches->to_replace;
        $toUse        = $matches->to_use;
        for ($i = 0; $i < count($toReplace); $i++) {
            $t       = $toUse[$i];
            $partial = $this->partialToUse($t->reference);
            if ($this->shouldSkipPartial($partial)) {
                $replacements[$i] = '';
                continue;
            }

            $p = new $partial();

            $replacements[$i] = $p($toUse[$i]);
        }

        $input = str_replace($toReplace, $replacements, $input);

        return new MarkdownInput($input);
    }

    private function matchesInContent(string $markdown): StdClass
    {
        $matches = [];
        preg_match_all('/' . self::REGEX_PATTERN . '/', $markdown, $matches);

        $partials = new StdClass();
        if (count($matches) > 0) {
            $partials->to_replace = $matches[0];
            $partials->to_use     = [];

            $toUse = $matches[1];
            for ($i = 0; $i < count($partials->to_replace); $i++) {
                $partials->to_use[] = $this->partialWithArgs($toUse[$i]);
            }
        }
        return $partials;
    }

    private function partialWithArgs(string $fullReference): StdClass
    {
        $pattern   = trim($fullReference);
        $reference = $pattern;
        $args      = new StdClass();
        if (str_contains($pattern, ':')) {
            list($reference, $a) = explode(':', $pattern, 2);
            $a = explode(',', $a);
            foreach ($a as $b) {
                list($key, $value) = explode('=', $b);
                $key   = trim($key);
                $value = trim($value);

                $args->{$key} = $value;
            }
        }

        $obj            = new StdClass();
        $obj->pattern   = $pattern;
        $obj->reference = $reference;
        $obj->args      = $args;

        return $obj;
    }

    private function partialToUse(string $toUse): string|false
    {
        if (array_key_exists('all', $this->partialsList)) {
            return $this->partialsList['all'];
        }

        if (array_key_exists($toUse, $this->partialsList)) {
            return $this->partialsList[$toUse];
        }
        return false;
    }

    private function shouldSkipPartial(string|null|false $partial): bool
    {
        if (
            is_string($partial) and
            class_exists($partial) and
            $interfaces = class_implements($partial) and
            is_array($interfaces) and
            in_array(PartialInterface::class, $interfaces)
        ) {
            return false;
        }
        return true;
    }
}
