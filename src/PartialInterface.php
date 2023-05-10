<?php

namespace Eightfold\CommonMarkPartials;

use StdClass;

interface PartialInterface
{
    public function __invoke(StdClass $internals): string;
}
