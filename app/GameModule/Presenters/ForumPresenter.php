<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Entities\Forum;
use Teddy\Forms\Form;


/**
 * @TODO: Get conversation
 */
class ForumPresenter extends BasePresenter
{

    /** @var \Teddy\Entities\Forum\Forums @inject */
    public $forumRepository;

    /** @var \Teddy\Entities\Forum\ForumPosts @inject */
    public $forumPostRepository;


    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->forums = $this->forumRepository->getForumsForUser($this->user);
    }

    /**
     * @param int $id
     */
    public function renderPost($id)
    {
        $post = $this->forumPostRepository->find($id);
        if (!$post->getForum()->canView($this->user)) {
            $this->flashMessage('You can\'t view this post', 'error');
            $this->redirect('default');
        }

        $this->template->post = $post;
    }

    /**
     * @param int $id
     */
    public function renderForum($id)
    {
        $forum = $this->forumRepository->find($id);
        if ($forum === null || !$forum->canView($this->user)) {
            $this->flashMessage('You can\'t view this forum or it doesn\'t exist', 'error');
            $this->redirect('default');
        }
        $this['newPostForm']['forum']->setDefaultValue($id);
        $this->template->forum = $forum;
    }

    /**
     * @param int $id
     */
    public function actionDelete($id)
    {
        $msg = $this->msgsRepository->find($id);
        if(!$msg || ($this->user != $msg->getTo() && $this->user == $msg->getFrom())) {
            $this->flashMessage('This message doesn\'t exist or wasn\'t intended for you.', 'error');
            $this->redirect('default');
        }

        $msg->deleteBy($this->user);
        $this->em->flush();

        $this->flashMessage('Message has been deleted');
        $this->redirect('default');
    }

    /**
     * Bans user from writing on this forum
     * @param User $user
     * @param string $reason
     * @param int $time [minutes]
     * @TODO
     */
    public function actionBanUser(User $user, $reason, $time)
    {

    }

    /**
     * @param int $id
     */
    public function renderDetail($id)
    {
        $msg = $this->msgsRepository->find($id);
        if(!$msg || ($this->user != $msg->getTo() && $this->user == $msg->getFrom())) {
            $this->flashMessage('This message doesn\'t exist or wasn\'t intended for you.', 'error');
            $this->redirect('default');
        }

        $msg->setUnread(false);
        $this->em->flush();

        $this->template->msg = $msg;

        $defaults = array(
            'to' => $msg->getSenderNick(),
            'subject' => $msg->getSubject(),
            'conversation' => $msg->getConversationId(),
        );

        $this['newMsgForm']['to']->setAttribute('readonly', 'readonly');
        $this['newMsgForm']['subject']->setAttribute('readonly', 'readonly');
        $this['newMsgForm']->setDefaults($defaults);
    }

    /**
     * @return Form
     */
    public function createComponentNewPostForm()
    {
        $form = new Form();
        $form->addHidden('conversation');
        $form->addHidden('forum');
        $form->addText('subject', 'Předmět');
        $form->addTextarea('text', 'Text')
            ->setRequired();
        $form->addSubmit('send', 'Submit');
        $form->onSuccess[] = $this->newPostFormSuccess;
        return $form;
    }

    /**
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function newPostFormSuccess(Form $form, $values)
    {
        $forum = new Forum($values['forum']);
        if (!$forum->canWrite($this->user)) {
            $this->flashMessage('You can\'t post here', 'error');
            $this->redirect('this');
        }

        $this->forumRepository->addPost($this->user, $forum, $values['subject'], $values['text'], $values['conversation']);
        $this->flashMessage('Post sent');
        $this->redirect('this');
    }

}