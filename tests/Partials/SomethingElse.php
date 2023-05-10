<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests\Partials;

use Eightfold\CommonMarkPartials\PartialInterface;

use Eightfold\CommonMarkPartials\PartialInput;

class SomethingElse implements PartialInterface
{
    public function __invoke(PartialInput $input, array $extras = []): string
    {
//         $site = $extras['site'];
//         $request = $extras['request'];
//
//         $s = (string) $site->testing;
        return '';
        // return $input->arguments()->content;
    }
}
