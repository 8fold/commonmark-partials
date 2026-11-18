<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use Eightfold\CommonMarkPartials\PartialInput;

final class PartialMatches
{
    private const REGEX_PATTERN = '{!!([\S\s]*?)!!}';

    /** @var list<string> */
    private array $toReplace = [];

    /** @var list<PartialInput> */
    private array $inputsToUse = [];

    public function __construct(
        private string $markdown
    ) {
        $matches = [];
        preg_match_all('/' . self::REGEX_PATTERN . '/', $markdown, $matches);

        $this->toReplace = $matches[0];

        $c = $matches[1];
        $totalToReplace = self::totalToReplace();
        for ($i = 0; $i < $totalToReplace; $i++) {
            $this->inputsToUse[] = new PartialInput($c[$i]);
        }
    }

    /**
     * @return list<string>
     */
    public function toReplace(): array
    {
        return $this->toReplace;
    }

    public function totalToReplace(): int
    {
        return count(self::toReplace());
    }

    /**
     * @return list<PartialInput>
     */
    public function inputsToUse(): array
    {
        return $this->inputsToUse;
    }

    public function inputAt(int $index): PartialInput
    {
        $inputsToUse = $this->inputsToUse();
        if (array_key_exists($index, $inputsToUse) === false) {
            return new PartialInput('');
        }
        return $inputsToUse[$index];
    }
}
