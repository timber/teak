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
      $contents .= '| Name | Priority | Description |' . self::NEWLINE;
      $contents .= '| --- | --- | --- |' . self::NEWLINE;
      foreach ($this->tags as $tag) {
         // if tags is empty

         if (strlen(trim($tag)) == 0) {
            continue;
         }

         $parsed = $this->parseHookTag($tag);

         $contents .= sprintf(
            '| <span class="hook-name"><code>%1$s</code></span> | '
               . '<span class="hook-priority">%2$s</span> | '
               . '<span class="hook-description">%3$s</span> |' . self::NEWLINE,
            $parsed['name'],
            $parsed['priority'],
            $parsed['description']
         );
      }
      $contents .= self::PARAGRAPH;
      $contents .= '</div>';
      $contents .= self::PARAGRAPH;
      return $contents;
   }

   /**
    * Parse a hook tag to extract name, priority, and description.
    *
    * @param string $tag
    * @return array
    */
   private function parseHookTag($tag)
   {
      // Extract description from parentheses
      $description = 'No description provided';
      if (preg_match('/\(([^)]+)\)/', $tag, $matches)) {
         $description = $matches[1];
         // Remove description from tag for further parsing
         $tag = preg_replace('/\([^)]+\)/', '', $tag);
      }

      // Split by dash to get name and priority
      $parts = explode('-', $tag, 2);
      $name = trim($parts[0]);
      $priority = isset($parts[1]) ? trim($parts[1]) : '10';

      // Default priority if empty
      if (empty($priority)) {
         $priority = '10';
      }

      return [
         'name' => $name,
         'priority' => $priority,
         'description' => $description
      ];
   }
}
