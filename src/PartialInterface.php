<?php

namespace Eightfold\CommonMarkPartials;

use Eightfold\CommonMarkPartials\PartialInput;

interface PartialInterface
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __invoke(PartialInput $input, array $extras = []): string;
}
