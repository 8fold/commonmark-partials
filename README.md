# 8fold Partials for {language or framework}

This library is an extension for the [CommonMark parser](https://github.com/thephpleague/commonmark) from the PHP League, adding the ability to insert renderable partials.

In other words, and with loads of humility, and we could be wrong, Markdown can feel more like a template engine. 

## Installation

```
composer require 8fold/commonmark-partials
```

## Usage

Create your partial class:

```php
<?php

namespace My\Namespace;

use Eightfold\CommonMarkPartials\PartialInterface;

class MyPartial
{
  public function __invoke(
    PartialInput $input, 
    array $extras = []
  ): string {
    return 'Will be processed like any other Markdown input.'
  }
}
```

Write your Markdown with your partial:

```markdown
# CommonMark Partials

{!! my_partial !!}
```

Instantiate CommonMark:

```php

use Eightfold\CommonMarkPartials\PartialsExtension;

use My\Namespace\MyPartial;

$environment = new Environment([
  'partials' => [
    'partials' => [
      'my_partial' => MyPartial::class
    ]
  ]
]);

$environment->addExtension(new CommonMarkCoreExtension())
  ->addExtension(new PartialsExtension());

$converter = new MarkdownConverter($environment);
```

Render the Markdown:

```php
$html = $converter->convertToHtml($markdown)->getContents();
```

Print the results:

```php
print $html;
```

The result should be:

```html
<h1>CommonMark Partials</h1>
<p>Will be processed like any other Markdown input.</p>
```

If you want to have your partial return HTML, you will need to update the CommonMark configuration to allow HTML:

```php
$environment = new Environment([
  'allow_html' => 'allow',
  'partials' => [
    'partials' => [
      'my_partial' => MyPartial::class
    ]
  ]
]);
```

## Details

We wanted to allow content creators to use Markdown to create the bulk of their content; through server-side rendering. Sometimes, we wanted (or needed) a way to inject HTML based on logic code and data.

We made it possible on one or two sites, which worked well. Now we've made it possible to use our Markdown library of choice (CommonMark).

## Other

{links or descriptions or license, versioning, and governance}
