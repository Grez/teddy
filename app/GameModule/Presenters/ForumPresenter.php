<?php

namespace Teddy\GameModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Entities\Forums\AccessDenied;
use Game\Entities\Forums\Forum;
use Game\Entities\Forums\ForumPost;
use Teddy\Entities\Forums\ForumsQuery;
use Teddy\Entities\Forums\PostsQuery;
use Teddy\Forms\Form;



class ForumPresenter extends \Game\GameModule\Presenters\BasePresenter
{

	/**
	 * @var \Teddy\Entities\Forums\Forums
	 * @inject
	 */
	public $forumFacade;

	/**
	 * @var \Teddy\Entities\Forums\ForumPosts
	 * @inject
	 */
	public $forumPostsFacade;



	public function renderDefault()
	{
		$this->template->forums = $this->forumFacade->getForumsWithUnreadPosts($this->user);
	}



	/**
	 * @param int (actually int) $id
	 */
	public function renderForum($id)
	{
		/** @var Forum $forum */
		$forum = $this->forumFacade->find($id);
		if ($forum === NULL || !$forum->canView($this->user)) {
			$this->warningFlashMessage('You can\'t view this forum or it doesn\'t exist');
			$this->redirect('default');
		}

		$this->forumFacade->updateLastVisit($this->user, $forum);

		$query = (new PostsQuery())
			->onlyFromForum($forum)
			->onlyNotDeleted()
			->orderByCreatedAt();
		$posts = $this->em->fetch($query)
			->applyPaginator($this['visualPaginator']->getPaginator(), 20);

		$this['newPostForm']['forum']->setDefaultValue($id);
		$this->template->forum = $forum;
		$this->template->posts = $posts;
	}



	/**
	 * @param int (actually string) $postId
	 */
	public function handleDelete($postId)
	{
		/** @var ForumPost $post */
		$post = $this->forumPostsFacade->find($postId);
		if (!$post || !$post->canDelete($this->user)) {
			$this->warningFlashMessage('This post doesn\'t exist or you can\'t delete it');
			$this->refreshPage();
			return;
		}

		$post->delete($this->user);
		$this->em->flush();

		$this->successFlashMessage('Post has been deleted');
		$this->refreshPage();
	}



	/**
	 * @return Form
	 */
	public function createComponentNewPostForm()
	{
		$form = new Form();
		$form->setAjax(TRUE);
		$form->addHidden('conversation');
		$form->addHidden('forum');
		$form->addText('subject', 'Subject');
		$form->addTextarea('text', 'Text')
			->setRequired();
		$form->addSubmit('send', 'Submit');
		$form->onSuccess[] = $this->newPostFormSuccess;
		return $form->setBootstrapRenderer();
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function newPostFormSuccess(Form $form, ArrayHash $values)
	{
		/** @var Forum $forum */
		$forum = $this->forumFacade->find($values->forum);
		if (!$forum || !$forum->canView($this->user)) {
			$this->warningFlashMessage('This forum doesn\'t exist');
			$this->refreshPage();
			return;
		}

		/** @var ForumPost|NULL $conversation */
		$conversation = $this->forumPostsFacade->find($values->conversation);

		try {
			$this->forumFacade->addPost($this->user, $forum, $values->subject, $values->text, $conversation);

		} catch (AccessDenied $e) {
			$this->warningFlashMessage($e->getMessage());
			$this->refreshPage();
			return;
		}

		$form->setValues([
			'conversation' => NULL,
			'subject' => NULL,
			'text' => NULL,
		]);
		$this->em->flush();
		$this->successFlashMessage('Post sent');
		$this->refreshPage();
	}

}
