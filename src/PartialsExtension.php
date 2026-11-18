<?php

declare(strict_types=1);

namespace Eightfold\CommonMarkPartials;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use League\Config\ConfigurationInterface;
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
        if (
            $config->exists(self::CONFIG_KEY) === false
            or is_array($config->get(self::CONFIG_KEY)) === false
        ) {
            return;
        }

        $partialsConfig = $config->get(self::CONFIG_KEY);

        $partialsList = [];
        if (array_key_exists('partials', $partialsConfig)) {
            $pList = $partialsConfig['partials'];
            if (is_array($pList) === false) {
                return;
            }
            foreach ($pList as $partialId => $partialClassName) {
                if (
                    is_string($partialId) === false
                    or is_string($partialClassName) === false
                ) {
                    continue;
                }
                $partialsList[$partialId] = $partialClassName;
            }
        }

        $extras = [];
        if (array_key_exists('extras', $partialsConfig)) {
            $e = $partialsConfig['extras'];
            if (is_array($e) === false) {
                return;
            }
            foreach ($e as $key => $value) {
                if (is_string($key) === false) {
                    continue;
                }
                $extras[$key] = $value;
            }
        }

        $environment->addEventListener(
            DocumentPreParsedEvent::class,
            new PartialsPrePocessor(
                new PartialsParser($partialsList, $extras)
            ),
            -100
        );
    }
}
