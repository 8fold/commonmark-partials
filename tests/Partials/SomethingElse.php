<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests\Partials;

use Eightfold\CommonMarkPartials\PartialInterface;

use Eightfold\CommonMarkPartials\PartialInput;

use Eightfold\CommonMarkPartials\Tests\Partials\ExtrasInstance;

class SomethingElse implements PartialInterface
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __invoke(PartialInput $input, array $extras = []): string
    {
        $site    = $extras['site'];
        $request = $extras['request'];

        $siteTest = (
            is_object($site)
            and is_a($site, ExtrasInstance::class)
            and $site->testing()
        )
            ? 'true'
            : 'false';

        $requestTest = (
            is_object($request)
            and is_a($request, ExtrasInstance::class)
            and $request->testing()
        )
            ? 'true'
            : 'false';

        return '<p>' . $siteTest . '</p>' . "\n" . '<p>' . $requestTest . '</p>';
    }
}
