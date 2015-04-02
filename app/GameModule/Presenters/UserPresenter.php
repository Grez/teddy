<?php

namespace App\GameModule\Presenters;

use GameModule;
use Nette\Application\UI\Form;

class UserPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $players = $this->users->getPlayersList();
        $this->template->players = $players;
    }

    /**
     * @param string $id (Player's nick)
     */
    public function renderDetail($id = '')
    {
        if ($id != '') {
            $player = $this->users->getByNick($id);
            if ($player == null) {
                $this->flashMessage('This user doesn\'t exist', 'error');
                $this->redirect('default');
            }
            $this->template->player = $player;
        }
    }

    /**
     * @TODO: data
     * @return Form
     */
    public function createComponentUpdateForm()
    {
        $form = new Form();
        $form->addHidden('user');
        $form->addText('name', 'Name');
        $form->addText('age', 'Age')
            ->addCondition(Form::FILLED)
                ->addRule(Form::NUMERIC);
        $form->addText('location', 'Origin');
        $form->addRadioList('gender', 'Gender', array(
            'unknown' => 'Do not show',
            'male' => 'Male',
            'female' => 'Female',
        ));
        $form->addUpload('avatar', 'Avatar')
            ->addRule(Form::IMAGE);
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