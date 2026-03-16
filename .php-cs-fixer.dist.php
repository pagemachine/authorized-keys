<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = (new Finder())
    ->in(__DIR__)
    ->ignoreVCSIgnored(true)
    ->exclude([
        'vendor',
    ])
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PHP80Migration' => true,
    ])
    ->setFinder($finder)
;
