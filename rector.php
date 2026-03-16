<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withPhpSets()
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ->withComposerBased(
        phpunit: true,
    )
    ->withAttributesSets()
;
