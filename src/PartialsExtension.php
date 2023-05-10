<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * Don't use PHP ability to infer namespace.
 */
use Eightfold\CommonMarkPartials\PartialsPrePocessor;
use Eightfold\CommonMarkPartials\PartialsParser;

/**
 * For the most part, this class should match the one from League CommonMark.
 *
 * Differences are annotated.
 */
final class PartialsExtension implements ConfigurableExtensionInterface
{
    private const CONFIG_KEY = 'partials';

    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema(self::CONFIG_KEY, Expect::structure([
            'partials' => Expect::arrayOf('string', 'string'),
            'extras'   => Expect::arrayOf('mixed', 'string')
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $config = $environment->getConfiguration();
        if ($config->exists(self::CONFIG_KEY)) {
            $partialsConfig = (array) $config->get(self::CONFIG_KEY);
            $environment->addEventListener(
                DocumentPreParsedEvent::class,
                // @phpstan-ignore-next-line
                new PartialsPrePocessor(new PartialsParser($partialsConfig)),
                -100
            );

        }
    }
}
