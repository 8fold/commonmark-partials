<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests\Partials;

use Eightfold\CommonMarkPartials\PartialInterface;

use Eightfold\CommonMarkPartials\PartialInput;

class Something implements PartialInterface
{
    public function __invoke(PartialInput $input, array $extras = []): string
    {
        return $input->arguments()->content;
    }
}
