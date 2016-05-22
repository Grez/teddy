<?php

namespace Teddy\Entities\Forums;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Game\Entities\Forums\ForumLastVisit;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Game\Entities\User\User;



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

	/**
	 * Indexed by User ids
	 *
	 * @var int[]
	 */
	protected $unreadPostsCount = [];

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



	/**
	 * @param ForumLastVisit $lastVisit
	 * @return $this
	 */
	public function addLastVisitBy(ForumLastVisit $lastVisit)
	{
		if (!$this->lastVisits->contains($lastVisit)) {
			$this->lastVisits->add($lastVisit);
		}
		return $this;
	}



	/**
	 * @param User $user
	 * @return int
	 */
	public function countUnreadPostsForUser(User $user)
	{
		$lastVisit = $this->getLastVisitBy($user);
		$criteria = (new Criteria())
			->andWhere(Criteria::expr()->isNull('deletedAt'));

		if ($lastVisit) {
			$criteria->where(Criteria::expr()->gt('createdAt', $lastVisit->getLastVisitAt()));
		}

		return $this->posts->matching($criteria)->count();
	}



	/**
	 * @param User $user
	 * @return int
	 */
	public function getUnreadPostsCountForUser(User $user)
	{
		if (isset($this->unreadPostsCount[$user->getId()])) {
			return $this->unreadPostsCount[$user->getId()];
		}

		$unreadPostsCount = $this->countUnreadPostsForUser($user);
		$this->setUnreadPostsCountForUser($user, $unreadPostsCount);
		return $unreadPostsCount;
	}



	/**
	 * @param User $user
	 * @param int $unreadPostsCount
	 * @return Forum
	 */
	public function setUnreadPostsCountForUser(User $user, $unreadPostsCount)
	{
		$this->unreadPostsCount[$user->getId()] = intVal($unreadPostsCount);
		return $this;
	}



	/**
	 * @param User $user
	 * @return bool
	 */
	public function hasUnreadPostsForUser(User $user)
	{
		return $this->getUnreadPostsCountForUser($user) > 0;
	}

}
