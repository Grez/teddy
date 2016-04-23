<?php

namespace Teddy\Services;

use Nette\Object;



/**
 * @TODO: resizing images
 */
class ImageService extends Object
{

	const AVATAR = 'avatar';

	/**
	 * @var string
	 */
	protected $imagePath;



	public function __construct($imagePath)
	{
		$this->imagePath = $imagePath;
	}



	/**
	 * Returns path to dir/filename
	 *
	 * @param string|NULL $filename
	 * @return string
	 */
	public function getAvatarPath($filename = NULL)
	{
		$path = $this->imagePath . '/' . self::AVATAR;
		@mkdir($path, 0777, TRUE);

		return $filename ? $path . '/' . $filename : $path;
	}

}
