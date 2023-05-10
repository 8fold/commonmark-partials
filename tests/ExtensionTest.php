<?php
declare(strict_types=1);

namespace Eightfold\CommonMarkPartials\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\CommonMarkPartials\PartialsExtension;

use Eightfold\CommonMarkPartials\Tests\Partials\Baseline;
use Eightfold\CommonMarkPartials\Tests\Partials\Something;
use Eightfold\CommonMarkPartials\Tests\Partials\SomethingElse;

use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

use StdClass;

class ExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function returning_markdown_is_parsed_and_rendered(): void
    {
        $environment = new Environment([
            'partials' => [
                'partials' => [
                    'all' => Baseline::class
                ]
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new PartialsExtension());

        $converter = new MarkdownConverter($environment);

        // baseline returns "World!"
        $markdown = <<<md
        # Hello

        {!! baseline !!}
        md;

        $expected = <<<html
        <h1>Hello</h1>
        <p>World!</p>

        html;

        $result = $converter->convert($markdown)->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function all_always_uses_the_all_partial_in_config(): void
    {
        $environment = new Environment([
            'partials' => [
                'partials' => [
                    'all'            => Baseline::class,
                    'something'      => Something::class
                ]
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new PartialsExtension());

        $converter = new MarkdownConverter($environment);

        // baseline returns "World!"
        $markdown = <<<md
        # Hello

        {!! baseline !!}

        {!! something !!}
        md;

        $expected = <<<html
        <h1>Hello</h1>
        <p>World!</p>
        <p>World!</p>

        html;

        $result = $converter->convert($markdown)->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_use_multiple_partials_and_argument_passing(): void
    {
        $environment = new Environment([
            'partials' => [
                'partials' => [
                    'baseline'  => Baseline::class,
                    'something' => Something::class
                ]
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new PartialsExtension());

        $converter = new MarkdownConverter($environment);

        // baseline returns "World!"
        $markdown = <<<md
        # Hello

        {!! baseline !!}

        {!! something:content=Some thing. !!}
        md;

        $expected = <<<html
        <h1>Hello</h1>
        <p>World!</p>
        <p>Some thing.</p>

        html;

        $result = $converter->convert($markdown)->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function removes_invalid_partial_content(): void
    {
        $environment = new Environment([
            'partials' => [
                'partials' => [
                    'baseline'    => Baseline::class,
                    'nonexistent' => 'Nonexistent\Class'
                ]
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new PartialsExtension());

        $converter = new MarkdownConverter($environment);

        $markdown = <<<md
        # Hello

        {!! baseline !!}

        {!! nonexistent !!}
        md;

        $expected = <<<html
        <h1>Hello</h1>
        <p>World!</p>

        html;

        $result = $converter->convert($markdown)->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @group focus
     */
    public function can_add_extras(): void
    {
        $site = new StdClass();
        $site->testing = true;

        $request = new StdClass();
        $request->testing = true;

        $environment = new Environment([
            'partials' => [
                'partials' => [
                    'baseline'    => Baseline::class,
                    'nonexistent' => 'Nonexistent\Class',
                    'something' => Something::class,
                    'something-else' => SomethingElse::class
                ],
                'extras' => [
                    'site'    => $site,
                    'request' => $request
                ]
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new PartialsExtension());

        $converter = new MarkdownConverter($environment);

        $markdown = <<<md
        # Hello

        {!! baseline !!}

        {!! nonexistent !!}

        {!! something:content=Some thing. !!}

        {!! something-else !!}
        md;

        $expected = <<<html
        <h1>Hello</h1>
        <p>World!</p>
        <p>true</p>
        <p>true</p>

        html;

        $result = $converter->convert($markdown)->getContent();

        $this->assertSame($expected, $result);
    }
}
