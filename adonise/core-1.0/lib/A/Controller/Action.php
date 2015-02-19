<?php

/**
 * Class A_Controller_Action
 *
 * @method A_Controller_Action setLayout(A_Block $layout)
 * @method A_Block getLayout()
 */
class A_Controller_Action extends A_Object
{
	public function __construct()
	{
		parent::__construct();

		$this->setLayout( A::registry('layout') );
	}
}