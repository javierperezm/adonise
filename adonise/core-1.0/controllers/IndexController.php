<?php
class Core_Controller_Index extends A_Controller_Action
{
	/**
	 * URL: http://host.com/core/index/index
	 */
	public function indexAction()
	{
		$this->getLayout()
			->addBlock(A_Block::create('core/page_html_head'))
			->addBlock(A_Block::create('core/page_html_body'))
			;
	}
}
