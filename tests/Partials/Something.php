<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests\Partials;

use Eightfold\CommonMarkPartials\PartialInterface;

use StdClass;

class Something implements PartialInterface
{
    public function __invoke(StdClass $internals): string
    {
        return $internals->args->content;
    }
}
