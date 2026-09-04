<?php

declare(strict_types=1);

namespace Tests\TestClasses;

/**
 * Class TestSeeTag
 *
 * @api
 */
class TestSeeTag
{
	/**
	 * @api
	 * @see TestSeeTag::other()
	 */
    public function seeMethod()
    {

    }

	/**
	 * @api
	 * @see TestSeeTag
	 */
    public function seeClass()
    {

    }

    public function other()
    {

    }
}
