<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->ignoreVCSIgnored(true)
    ->exclude([
        'vendor',
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PHP80Migration' => true,
    ])
    ->setFinder($finder)
;
