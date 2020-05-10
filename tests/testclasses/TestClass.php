<?php

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
    /**
     * Public property summary.
     *
	 * @api
	 * @var string Public property description.
	 */
	public $public_property;

    /**
     * TestClass constructor.
     */
    public function __construct() {

    }

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
    public function default_method( $string_var, $int_var ) {
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
    public function string_empty( $string_var ) {}

    /**
     * Function summary
     *
     * @param string $string_var Parameter description.
     * @return string $string_return Return description.
     */
    public function doc_param_string_return_string( $string_var ) {
        $string_return = '';

        return $string_return;
    }
}
