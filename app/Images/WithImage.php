<?php

namespace Teddy\Images;

use Kdyby;
use Nette;



interface WithImage
{

	/**
	 * @param ImageService $imageService
	 * @param string|NULL $variant
	 * @return string
	 */
	public function resolveImage(ImageService $imageService, $variant = NULL);

}
