<?php

declare(strict_types=1);

namespace Tests\TestClasses;

/**
 * Test Class Summary
 *
 * Test Class Description.
 * The Description can be multiline.
 *
 * @api
 * @example
 * ```php
 * $var = function();
 * ```
 *
 * Text in between code examples
 *
 * ```twig
 * <p>{{ var }}</p>
 * ```
 */
class TestClass
{
    use TestTrait;

    /**
     * Public property summary.
     *
     * @api
     * @var string Public property description.
     */
    public $public_property;

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     *
     * @see   TestClass::default_method() relied on.
     * @link  URL
     * @global string $global_var_string Description.
     * @global int    $global_var_int    Description.
     *
     * @param string  $string_var              Description.
     * @param int     $int_var              Optional. Description. Default.
     *
     * @return string Description.
     */
    public function default_method($string_var, $int_var)
    {
        return 'Description';
    }

    public function no_docblock_no_param_empty() {}

    /**
     * Function summary.
     *
     * Function description.
     */
    public function no_param_empty() {}

    /**
     * Function summary.
     *
     * @return void
     */
    public function no_param_return_empty_void() {}

    /**
     * Function summary
     *
     * @param string $string_var Parameter description.
     */
    public function string_empty($string_var) {}

    /**
     * Function summary
     * @param array Invalid item that should get reported and not trigger an error.
     * @param string $string_var Parameter description.
     * @return string $string_return Return description.
     */
    public function doc_param_string_return_string($string_var)
    {
        return '';
    }

    /**
     * Picture element.
     *
     * @param array $args {
     *     Additional arguments to help filter the picture element.
     *
     *     @type object post The post object.
     *     @type object loop The loop object.
     *     @type string card_type The card type.
     *     @type int attachment_id The attachment ID.
     *     @type string picture_classes The picture classes.
     *     @type string image_size The image size.
     *     @type image_sizes image_sizes The image sizes attribute.
     *     @type bool focalpoint Whether or not to use the focal point.
     * }
     */
    public function picture_element($args) {}
}
