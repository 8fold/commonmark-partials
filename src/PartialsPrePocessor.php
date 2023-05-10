<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Event\DocumentPreParsedEvent;

use Eightfold\CommonMarkPartials\PartialsParser;

final class PartialsPrePocessor
{
    private PartialsParser $parser;

    public function __construct(PartialsParser $parser)
    {
        $this->parser = $parser;
    }

    public function __invoke(DocumentPreParsedEvent $event): void
    {
        $content = $event->getMarkdown()->getContent();

        $parsed = $this->parser->parse($content);

        $event->replaceMarkdown($parsed);
    }
}
