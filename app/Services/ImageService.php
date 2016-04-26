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
	protected $wwwDir;

	/**
	 * @var string
	 */
	protected $imagePath;



	public function __construct($wwwDir, $imagePath)
	{
		$this->imagePath = $imagePath;
		$this->wwwDir = $wwwDir;
	}



	/**
	 * Gets path for frontend
	 *
	 * @param $filename string
	 * @return string
	 */
	public function getAvatar($filename)
	{
		$path = $this->imagePath . '/' . self::AVATAR;
		return $path . '/' . $filename;
	}



	/**
	 * Returns path to dir/filename
	 *
	 * @param string|NULL $filename
	 * @return string
	 */
	public function getAvatarPath($filename = NULL)
	{
		$path = $this->wwwDir . $this->imagePath . '/' . self::AVATAR;
		@mkdir($path, 0777, TRUE);

		return $filename ? $path . '/' . $filename : $path;
	}

}
