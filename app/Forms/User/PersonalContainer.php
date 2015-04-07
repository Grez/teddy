<?php

namespace Teddy\Forms\User;

use Nette;
use Teddy\Forms\Form;

class PersonalContainer extends Nette\Forms\Container
{

    public function __construct()
    {
        parent::__construct();

        $this->addText('name', 'Name');
        $this->addText('age', 'Age')
            ->addCondition(Form::FILLED)
            ->addRule(Form::NUMERIC);
        $this->addText('location', 'Location');
        $this->addRadioList('gender', 'Gender', array(
            0 => 'Do not show',
            1 => 'Male',
            2 => 'Female',
        ));
        $this->addUpload('avatar', 'Avatar')
            ->addCondition(Form::FILLED)
            ->addRule(Form::IMAGE);
    }

}