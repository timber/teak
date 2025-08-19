<?php

namespace Teak\Compiler\Tag;

use Teak\Compiler\CompilerInterface;
use Teak\Reflection\DocBlock;

/**
 * Class Link
 */
class Hooked implements CompilerInterface
{
   /**
    * @var \phpDocumentor\Reflection\DocBlock\Tags\Link
    */
   protected $tags;

   /**
    * Link constructor.
    *
    * @param \phpDocumentor\Reflection\DocBlock $docBlock
    */
   public function __construct($docBlock)
   {
      $docBlock = new DocBlock($docBlock);
      $this->tags = $docBlock->getTags('hooked');
   }

   /**
    * Compile.
    *
    * @return string
    */
   public function compile()
   {
      $contents = '';

      if (empty($this->tags)) {
         return $contents;
      }

      $contents .= '**Hooked** ' . self::PARAGRAPH;
      $contents .= '<div class="table-hooked table-responsive">';
      $contents .= self::PARAGRAPH;
      $contents .= '| Name | priority | Description |' . self::NEWLINE;
      $contents .= '| --- | --- | --- |' . self::NEWLINE;
      foreach ($this->tags as $tag) {

         // hook name is everything before the first -
         $hook_name = strstr($tag, '-', true);

         // priority is everything between the first - and (
         $hook_priority = substr($tag, strlen($hook_name) + 1, strpos($tag, '(') - strlen($hook_name) - 1);

         // description is everything between ( and )
         $hook_description = substr($tag, strpos($tag, '(') + 1, strrpos($tag, ')') - strpos($tag, '(') - 1);

         $contents .= sprintf(
            '| <span class="hook-name"><code>%1$s</code></span> | '
               . '<span class="hook-priority">%2$s</span> | '
               . '<span class="hook-description">%3$s</span> |' . self::NEWLINE,
            $hook_name,
            $hook_priority,
            $hook_description
         );
      }
      $contents .= self::PARAGRAPH;
      $contents .= '</div>';
      $contents .= self::PARAGRAPH;
      return $contents;
   }
}
