<?php

namespace Teak\Compiler\Param;

use Teak\Compiler\CompilerInterface;
use Teak\Compiler\SanitizeTrait;
use Teak\Compiler\Warnings;

class InvalidTag implements CompilerInterface
{
   use SanitizeTrait;

   private $tag;

   public function __construct($tag)
   {
      $this->tag = $tag;
   }

   public function compile()
   {
      $details = [(string) $this->tag];

      if (method_exists($this->tag, 'getException') && $this->tag->getException()) {
         array_unshift($details, $this->tag->getException()->getMessage());
      }

      $error = implode(' - ', array_filter($details));

      if (empty($error)) {
         $error = 'Invalid param tag';
      }

      Warnings::getInstance()->add('Invalid @param tag - ' . $error);

      return '| [Invalid Tag] | | Error: ' . $this->sanitizeTextForTable($error) . ' |' . self::NEWLINE;
   }
}
