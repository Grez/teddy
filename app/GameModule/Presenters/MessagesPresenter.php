<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Forms\Form;
use Teddy\Entities\PM\Messages;


/**
 * @TODO: Get conversation
 */
class MessagesPresenter extends BasePresenter
{

    /** @var Messages @inject */
    public $msgsRepository;

    /** @var array */
    protected $msgs = array();


    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->msgs = $this->msgsRepository->getMessagesForUser($this->user);
    }

    public function renderNew()
    {
        $params = $this->getRequest()->getParameters();
        if (isset($params['to'])) {
            $this['newMsgForm']['to']->setValue($params['to'])->setAttribute('readonly', 'readonly');
        }
    }

    /**
     * @param int $id
     */
    public function renderDetail($id)
    {
        $msg = $this->msgsRepository->find($id);
        if(!$msg || ($this->user != $msg->getTo() && $this->user != $msg->getFrom())) {
            $this->flashMessage('This message doesn\'t exist or wasn\'t intended for you.', 'danger');
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
     * @param int $id
     */
    public function actionDelete($id)
    {
        $msg = $this->msgsRepository->find($id);
        if(!$msg || ($this->user != $msg->getTo() && $this->user != $msg->getFrom())) {
            $this->flashMessage('This message doesn\'t exist or wasn\'t intended for you.', 'danger');
            $this->redirect('default');
        }

        $msg->deleteBy($this->user);
        $this->em->flush();

        $this->flashMessage('Message has been deleted');
        $this->redirect('default');
    }

    /**
     * @return Form
     */
    public function createComponentNewMsgForm()
    {
        $form = new Form();
        $form->addHidden('conversation');
        $form->addText('to', 'Recipient')
            ->setRequired();
        $form->addText('subject', 'Předmět')
            ->setRequired();
        $form->addTextarea('text', 'Message')
            ->setRequired();
        $form->addSubmit('send', 'Submit');
        $form->onSuccess[] = $this->newMsgFormSuccess;
        return $form;
    }

    /**
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function newMsgFormSuccess(Form $form, $values)
    {
        $recipient = $this->users->getByNick($values['to']);
        if(!$recipient) {
            $this->flashMessage('This user doesn\'t exist.', 'danger');
            $this->redirect('this');
        }

        $this->msgsRepository->createMessage($this->user, $recipient, $values['subject'], $values['text'], $values['conversation']);
        $this->flashMessage('Message sent');
        $this->redirect('default');
    }

}
