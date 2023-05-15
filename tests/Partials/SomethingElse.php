<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests\Partials;

use Eightfold\CommonMarkPartials\PartialInterface;

use Eightfold\CommonMarkPartials\PartialInput;

class SomethingElse implements PartialInterface
{
    public function __invoke(PartialInput $input, array $extras = []): string
    {
        $site = $extras['site'];
        $request = $extras['request'];

        $siteTest = 'false';
        if ($site->testing()) {
            $siteTest = 'true';
        }

        $requestTest = 'false';
        if ($request->testing()) {
            $requestTest = 'true';
        }
        return '<p>' . $siteTest . '</p>'. "\n" . '<p>' . $requestTest . '</p>';
    }
}
