<?php

namespace Eightfold\CommonMarkPartials;

use Eightfold\CommonMarkPartials\PartialInput;

interface PartialInterface
{
    public function __invoke(PartialInput $input, array $extras = []): string;
}
