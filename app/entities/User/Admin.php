<?php

namespace Teddy\Entities\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;



class Admin extends \Game\Entities\User\Player
{

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $adminDescription = '';

	/**
	 * @ORM\OneToMany(targetEntity="\Game\Entities\User\AdminPermission", mappedBy="admin", cascade={"persist", "remove"})
	 * @var AdminPermission[]|ArrayCollection
	 */
	protected $adminPermissions;



	/**
	 * Checks if user is allowed in $presenter in AdminModule
	 *
	 * @param string $presenter
	 * @return bool
	 */
	public function isAllowed($presenter)
	{
		foreach ($this->adminPermissions as $permission) {
			if ($permission->getPresenter() == $presenter) {
				return TRUE;
			}
		}

		return FALSE;
	}



	/**
	 * @param bool $array
	 * @return array|ArrayCollection
	 */
	public function getAdminPermissions($array = FALSE)
	{
		if (!$array) {
			return $this->adminPermissions;
		} else {
			$adminPermissions = [];
			foreach ($this->adminPermissions as $adminPermission) {
				$adminPermissions[] = $adminPermission->getPresenter();
			}
			return $adminPermissions;
		}
	}

}
