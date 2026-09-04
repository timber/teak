<?php

declare(strict_types=1);

namespace Tests\TestClasses;

/**
 * Test Trait Summary
 *
 * Test Trait Description.
 * This trait provides common functionality.
 *
 * @api
 * @since 1.0.0
 */
trait TestTrait
{
   /**
    * Trait property summary.
    *
    * @api
    * @var string Trait property description.
    */
   public $trait_property;

   /**
    * Trait method summary.
    *
    * Trait method description.
    *
    * @api
    * @since 1.0.0
    *
    * @param string $param Parameter description.
    * @return string Return description.
    */
   public function trait_method($param)
   {
      return $param;
   }

   /**
    * Protected trait method.
    *
    * @param int $value Value description.
    * @return int
    */
   protected function protected_trait_method($value)
   {
      return $value;
   }
}
