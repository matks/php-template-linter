Templating linter for php
=========================

[![Build Status](https://api.travis-ci.org/matks/php-template-linter.svg?branch=master)](https://travis-ci.org/matks/php-template-linter)


# Why

After a long time I did not find a linter that is able to parse and fix
Smarty and Twig templates. So I decided to build one.

I wanted 3 things:
- the linter must be able to find issues
- the linter must be as configurable as possible
- the linter must be able to fix most issues to avoid wasting the developer's time

Primary usage (WIP) is to fix indentation. I might add more features later.

# Usage

## Install

`$ composer install`

## Run

`$ php linter fix <target> [--config] [--dry-run]`

See `$ php linter fix -h` for advanced help

### Pass custom configuration

You can provide your own configuration by using `--config` option. You must
pass the filepath of a PHP configuration file.

The configuration file must return an instance of `LineLinterConfiguration`.
You can build this object in any way, however one suggested simple way to built it
is to use `LineLinterConfiguration::fromArray()` method - see example below.

```
// config-custom.php
<?php

use Matks\PHPTemplateLinter\LineLinterConfigurationItem;
use Matks\PHPTemplateLinter\LineLinterConfiguration;

$configuration = [
    LineLinterConfigurationItem::TYPE_IGNORE_CHAR => [
        '{#', '*'
    ],
    LineLinterConfigurationItem::TYPE_OPENING_CHAR => [
        '<div', '<form',
        '<h', '<i', '<p', '<a',
        '<thead', '<td', '<tr', '<th', '<table', '<tbody',
        '<span', '<button', '<label',
    ],
    LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR => [
        '{% else'
    ],
    LineLinterConfigurationItem::TYPE_CLOSING_CHAR => [
        '{% endblock', '{% endif', '{% endfor',
        '</div', '</form',
        '</h', '</i', '</p', '</a',
        '</thead', '</td', '</tr', '</th', '</table', '</tbody',
        '</span', '</button', '</label',
    ],
];

return LineLinterConfiguration::fromArray($configuration, 2);

```

# Tests

To run all tests, you can use bash script `tests/run_all_tests.sh`

## Unit

Run `$ vendor/bin/phpunit tests/`

## Integration

Run
```
$ php tests/Integration/check-samples.php
$ php tests/Integration/check-configurations.php
```

## Acceptance

Run
```
$ php tests/Acceptance/run.php
```

# Quality

Run
```
$ vendor/bin/phpstan analyse src tests --level=5
```

## Insights

Nothing fancy behind the curtain. I'm mostly using `strpos()` or
`preg_match()` to parse files.
