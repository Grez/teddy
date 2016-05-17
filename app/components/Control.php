<?php

namespace Teddy\Components;

use Nette;
use Teddy\TemplateHelpers;



class Control extends Nette\Application\UI\Control
{

	/**
	 * @var TemplateHelpers
	 */
	protected $templateHelpers;



	public function __construct(TemplateHelpers $templateHelpers, IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->templateHelpers = $templateHelpers;
	}



	protected function createTemplate()
	{
		$template = parent::createTemplate();
		$this->templateHelpers->register($template->getLatte());
		return $template;
	}

}
