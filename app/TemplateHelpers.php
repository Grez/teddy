<?php

namespace Teddy;

use Teddy\Images\ImageService;
use Teddy\Images\WithImage;



class TemplateHelpers extends \Nette\Object
{

	/**
	 * @var ImageService
	 */
	protected $imageService;



	public function __construct(ImageService $imageService)
	{
		$this->imageService = $imageService;
	}



	/**
	 * Registers all filters
	 *
	 * @param \Latte\Engine $engine
	 */
	public function register(\Latte\Engine $engine)
	{
		$engine->addFilter(NULL, function ($filterName) use ($engine) {
			if (method_exists($this, $filterName)) {
				$engine->addFilter($filterName, [$this, $filterName]);
			}
		});
	}



	/**
	 * @param WithImage $hasImage
	 * @param null $variant
	 * @return string
	 */
	public function img(WithImage $hasImage, $variant = NULL)
	{
		return $hasImage->resolveImage($this->imageService, $variant);
	}

}
