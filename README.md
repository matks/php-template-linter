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

TODO

# Tests

## Unit

TODO

## Integration

Run `$ php tests/Integration/run.php`

## Insights

Nothing fancy behind the curtain. I'm mostly using `strpos()` or
`preg_match()` to parse files.
