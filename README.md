Templating linter for php
=========================

# Why

After a long time I did not find a linter that is able to parse and fix
Smarty and Twig templates. So I decided to build one.

Primary usage (WIP) is to fix indentation. I might add more features later.

# Usage

TODO

# Tests

## Unit

Run `$ vendor/bin/phpunit tests/`

## Integration

Run `$ php tests/Integration/run.php`

## Insights

Nothing fancy behind the curtain. I'm mostly using `strpos()` or
`preg_match()` to parse files.
