#!/bin/bash

 vendor/bin/phpunit tests/ &&
 php tests/Integration/check-samples.php &&
 php tests/Integration/check-configurations.php &&
 php tests/Acceptance/run.php &&
 vendor/bin/phpstan analyse src tests --level=5