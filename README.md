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

`$ php linter fix <target>`

See `$ php linter fix -h` for available options

# Tests

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

## Insights

Nothing fancy behind the curtain. I'm mostly using `strpos()` or
`preg_match()` to parse files.

## TODO

- enable recursive application usage
- use Logger instead of echo
- use OutputInterface instead of echo
