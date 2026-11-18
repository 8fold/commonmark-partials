<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Parser\MarkdownParserInterface;

use stdClass;

use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Parser\Cursor;

use Eightfold\CommonMarkPartials\PartialInterface;
use Eightfold\CommonMarkPartials\PartialInput;
use Eightfold\CommonMarkPartials\PartialMatches;

final class PartialsParser
{
    /**
     * Config keys
     *
     * @param array<string, string> $partials
     * @param array<string, mixed> $extras
     */
    final public function __construct(
        private readonly array $partials,
        private readonly array $extras = []
    ) {
    }

    public function parse(string $input): MarkdownInput
    {
        $matches = new PartialMatches($input);
        $totalToReplace = $matches->totalToReplace();
        if ($totalToReplace === 0) {
            return new MarkdownInput($input);
        }

        $replacements = [];
        for ($i = 0; $i < $totalToReplace; $i++) {
            $partialInput = $matches->inputAt($i);

            $partialClassName = self::partialToUse($partialInput->reference());
            if (self::shouldSkipPartial($partialClassName)) {
                $replacements[$i] = '';
                continue;
            }

            $p = new $partialClassName();
            if (is_callable($p) === false) {
                $replacements[$i] = '';
                continue;
            }

            $replacement = $p($partialInput, $this->extras);
            if (is_string($replacement) === false) {
                $replacements[$i] = '';
                continue;
            }
            $replacements[$i] = $replacement;
        }

        $input = str_replace($matches->toReplace(), $replacements, $input);

        return new MarkdownInput($input);
    }

    private function partialToUse(string $reference): string
    {
        if (array_key_exists('all', $this->partials)) {
            return $this->partials['all'];
        }

        if (array_key_exists($reference, $this->partials)) {
            return $this->partials[$reference];
        }
        return '';
    }

    private function shouldSkipPartial(string|null|false $partial): bool
    {
        if (is_string($partial) === false) {
            return true;
        }

        if ($partial === '') {
            return true;
        }

        if (class_exists($partial) === false) {
            return true;
        }

        $p = new $partial();
        if (is_callable($p) === false) {
            return true;
        }

        $interfaces = class_implements($partial);
        return (
            is_array($interfaces)
            and in_array(PartialInterface::class, $interfaces, true)
        ) === false;
    }
}
