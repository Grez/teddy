<?php

namespace Teddy\Entities\Forums;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\User;



/**
 * @ORM\MappedSuperclass()
 */
abstract class Forum extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\OneToMany(targetEntity="\Game\Entities\Forums\ForumPost", mappedBy="forum")
	 * @var ForumPost[]|ArrayCollection
	 */
	protected $posts;

	/**
	 * @ORM\OneToMany(targetEntity="\Game\Entities\Forums\ForumLastVisit", mappedBy="forum", fetch="EXTRA_LAZY")
	 * @var ForumLastVisit[]|ArrayCollection
	 */
	protected $lastVisits;

	protected $forums = [
		Forums::ADMIN_ANNOUNCEMENTS => 'Admin announcements',
		Forums::WORLD_CHAT          => 'World chat',
		Forums::ADMINS              => 'Admins',
		Forums::HELPDESK            => 'Helpdesk',
		Forums::ALTERNATIVE         => 'Alternativ',
		Forums::BUGS                => 'Bugs & Features',
	];



	public function getName()
	{
		if (isset($this->forums[$this->getId()])) {
			return $this->forums[$this->getId()];
		} else {
			throw new Nette\InvalidArgumentException('Unknown forum id');
		}
	}



	/**
	 * Can User view this Forums?
	 *
	 * @param User $user
	 * @return bool
	 */
	public function canView(User $user)
	{
		switch ($this->getId()) {
			case Forums::ADMINS:
				if ($user->isAdmin()) {
					return TRUE;
				}
				break;

			case Forums::ADMIN_ANNOUNCEMENTS:
			case Forums::WORLD_CHAT:
			case Forums::HELPDESK:
			case Forums::ALTERNATIVE:
			case Forums::BUGS:
				return TRUE;
				break;

			default:
				return FALSE;
		}
	}



	/**
	 * Can User write on this Forums?
	 *
	 * @param User $user
	 * @return bool
	 */
	public function canWrite(User $user)
	{
		switch ($this->getId()) {
			case Forums::ADMINS:
			case Forums::ADMIN_ANNOUNCEMENTS:
			case Forums::ALTERNATIVE:
				return $user->isAdmin();

			case Forums::WORLD_CHAT:
			case Forums::HELPDESK:
			case Forums::BUGS:
				return TRUE;
				break;

			default:
				return FALSE;
		}
	}



	/**
	 * @param User $user
	 * @return ForumLastVisit
	 * @TODO
	 */
	public function getLastVisitBy(User $user)
	{
		$criteria = (new Criteria())
			->where(Criteria::expr()->eq('user', $user))
			->setMaxResults(1);

		return $this->lastVisits->matching($criteria)->first();
	}

}
