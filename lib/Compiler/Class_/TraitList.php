<?php

namespace Teak\Compiler\Class_;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\ClassReflection;

/**
 * Class TraitList
 */
class TraitList implements CompilerInterface
{
   /**
    * @var ClassReflection
    */
   public $class;

   /**
    * TraitList constructor.
    *
    * @param ClassReflection $class
    */
   public function __construct($class)
   {
      $this->class = $class;
   }

   /**
    * Compile.
    *
    * @return string
    */
   public function compile()
   {
      $contents = '';

      $usedTraits = $this->class->getUsedTraits();

      if (!empty($usedTraits)) {
         $traitNames = array_map(function ($trait) {
            return '`' . ltrim((string) $trait, '\\') . '`';
         }, $usedTraits);

         if (count($traitNames) === 1) {
            $contents .= '*This class uses the trait ' . reset($traitNames) . '*';
         } else {
            $contents .= '*This class uses the traits ' . implode(', ', $traitNames) . '*';
         }
      }

      return $contents . self::BREAK;
   }
}
