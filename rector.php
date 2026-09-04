<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\CodeQuality\Rector\FuncCall\SingleInArrayToCompareRector;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
   ->withPaths([
      __DIR__ . '/lib',
      __DIR__ . '/tests',
   ])
   ->withSets([
      SetList::PHP_83,
      SetList::CODE_QUALITY,
      SetList::DEAD_CODE,
   ])
   ->withSkip([
      DisallowedEmptyRuleFixerRector::class,
      SingleInArrayToCompareRector::class,
   ]);
