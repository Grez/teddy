<?php

namespace App\GameModule\Presenters;

use GameModule;
use Teddy\Forms\Form;

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
     * @return Form
     */
    protected function createComponentUpdateUserForm()
    {
        $form = new Form();
        $form['user'] = new \Teddy\Forms\User\UserContainer();
        $form['user']['personal'] = new \Teddy\Forms\User\PersonalContainer();
        $form->addSubmit('send', 'Submit');
        $form->onSuccess[] = $this->updateUserFormSuccess;
        $form->bindEntity($this->user);
        return $form;
    }

    /**
     * @TODO: img upload
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function updateUserFormSuccess(Form $form, $values)
    {
        $user = $this->users->find($values['user']['id']);
        if (!$user->canEdit($this->user)) {
            $this->flashMessage('You can\'t edit this user', 'error');
            $this->redirect('this');
        }

        $this->users->update($user, $values);
        $this->flashMessage('Your info has been updated.');
        $this->redirect('this');
    }

}