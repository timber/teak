<?php

namespace Teak\Compiler\Param;

use phpDocumentor\Reflection\DocBlockFactory;

class ArrayDefinition
{
   public function compileDescription($description, $namePrefix = '$')
   {
      $description = trim($description, '{}');
      $description = $this->normalizeTypeTags($description);

      $docBlockFactory = DocBlockFactory::createInstance();
      $paramArray = new ParamArray($docBlockFactory->create($description), $namePrefix);

      return $paramArray->compile();
   }

   public function compileArrayShape($type, $namePrefix = '')
   {
      $docBlock = $this->arrayShapeToDocBlock((string) $type);

      if ('' === $docBlock) {
         return '';
      }

      return $this->compileDescription($docBlock, $namePrefix);
   }

   public function normalizeArrayShapeType($type)
   {
      return preg_replace('/array\{.+\}/', 'array', (string) $type);
   }

   private function normalizeTypeTags($description)
   {
      $description = preg_replace(
         '/(^|\R)([ \t]*)@type([ \t]+)([^\s]+)([ \t]+)(?!\$)([A-Za-z_][A-Za-z0-9_]*)(?=[ \t]|$)/',
         '$1$2@param$3$4$5\$$6',
         $description
      );

      return str_replace('@type', '@param', $description);
   }

   private function arrayShapeToDocBlock($type)
   {
      if (!preg_match('/array\{(.+)\}/', $type, $matches)) {
         return '';
      }

      $params = [];

      foreach ($this->splitArrayShapeFields($matches[1]) as $field) {
         if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*|\'[^\']+\'|"[^"]+")\??\s*:\s*(.+)$/', trim($field), $fieldMatches)) {
            continue;
         }

         $fieldName = trim($fieldMatches[1], '\'"');

         if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $fieldName)) {
            continue;
         }

         $params[] = '@param ' . trim($fieldMatches[2]) . ' $' . $fieldName;
      }

      return implode("\n", $params);
   }

   private function splitArrayShapeFields($shape)
   {
      $fields = [];
      $depth = 0;
      $field = '';
      $characters = str_split($shape);

      foreach ($characters as $character) {
         if (in_array($character, ['{', '[', '(', '<'], true)) {
            ++$depth;
         }

         if (in_array($character, ['}', ']', ')', '>'], true)) {
            --$depth;
         }

         if (',' === $character && 0 === $depth) {
            $fields[] = $field;
            $field = '';
            continue;
         }

         $field .= $character;
      }

      if ('' !== trim($field)) {
         $fields[] = $field;
      }

      return $fields;
   }
}
