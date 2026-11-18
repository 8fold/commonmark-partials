<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Event\DocumentPreParsedEvent;

use Eightfold\CommonMarkPartials\PartialsParser;

final class PartialsPrePocessor
{
    public function __construct(private readonly PartialsParser $parser)
    {
    }

    public function __invoke(DocumentPreParsedEvent $event): void
    {
        $content = $event->getMarkdown()->getContent();

        $event->replaceMarkdown(
            self::parser()->parse($content)
        );
    }

    private function parser(): PartialsParser
    {
        return $this->parser;
    }
}
