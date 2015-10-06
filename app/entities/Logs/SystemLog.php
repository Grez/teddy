<?php

namespace Teddy\Entities\Logs;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class SystemLog extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $script;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $action = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $description = '';

	/**
	 * @ORM\Column(type="datetime")
	 * Generated in __construct()
	 */
	protected $date;

	public static $scripts = array(
		1 => 'cron_minute',
		2 => 'cron_hour',
		3 => 'cron_midnight',
		4 => 'cron_night',
		5 => 'migration',
		6 => 'restart',
	);

	public static $actions = array(
		1 => 'finished',
	);



	public function __construct()
	{
		$this->date = new \DateTime();
	}



	public static function getActionById($id)
	{
		if (array_key_exists($id, self::$actions)) {
			return self::$actions[$id];
		}

		throw new \InvalidArgumentException('Unknown action ID "' . $id . '". Check \Teddy\Model\Logs::$actions');
	}



	public static function getActionId($action)
	{
		foreach (self::$actions as $id => $name) {
			if ($name == $action) {
				return $id;
			}
		}

		throw new \InvalidArgumentException('Unknown action name "' . $action . '". Check \Teddy\Model\Logs::$scripts');
	}



	public static function getScriptById($id)
	{
		if (array_key_exists($id, self::$scripts)) {
			return self::$scripts[$id];
		}

		throw new \InvalidArgumentException('Unknown script ID "' . $id . '". Check \Teddy\Model\Logs::$scripts');
	}



	public static function getScriptId($script)
	{
		foreach (self::$scripts as $id => $name) {
			if ($name == $script) {
				return $id;
			}
		}

		throw new \InvalidArgumentException('Unknown script name "' . $script . '". Check \Teddy\Model\Logs::$scripts');
	}

}
