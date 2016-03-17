<?php

namespace Teddy\Entities\Forum;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\User;



/**
 * @ORM\Entity()
 */
class Forum extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\OneToMany(targetEntity="ForumPost", mappedBy="forum")
	 */
	protected $posts;

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
	 * Can User view this Forum?
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
	 * Can User write on this Forum?
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
	 * Returns post in the forum, w/o deleted
	 *
	 * @return array(ForumPost)
	 */
	public function getPosts()
	{
		$posts = $this->posts;
		foreach ($posts as $id => $post) {
			if ($post->isDeleted()) {
				unset($posts[$id]);
			}
		}
		return $posts;
	}



	/**
	 * @TODO
	 * @param User $user
	 * @return int
	 */
	public function getUnreadPosts(User $user)
	{
		return 0;
	}
}
