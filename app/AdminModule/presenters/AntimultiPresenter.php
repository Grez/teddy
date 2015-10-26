<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Entities\User\Login;
use Teddy\Entities\User\LoginListQuery;
use Teddy\Entities\User\UserAgent;
use Teddy\Forms\Form;



/**
 * @TODO: kontrolor, zahraničí, thor hammer
 */
class AntimultiPresenter extends BasePresenter
{

	/**
	 * Shows list of new users, email, similar passwords?, user agent, IP
	 */
	public function renderNewProfiles()
	{

	}



	public function renderDefault($type, $success, $text)
	{
		$query = (new LoginListQuery());

		if ($success) {
			$this['loginForm']['success']->setDefaultValue($success);
			if ($success === 'success') {
				$query->onlySuccessful();
			}

			if ($success === 'success') {
				$query->onlyUnsuccessful();
			}
		}

		if ($type) {
			$this['loginForm']['type']->setDefaultValue($type);
		}


		if ($text) {
			$this['loginForm']['text']->setDefaultValue($text);
			switch ($type) {
				case 'user':
					$user = $this->users->getByNick($text);
					$query->byUser($user);
					break;
				case 'userAgent':
					$userAgent = $this->em->getRepository(UserAgent::class)->findOneBy(['userAgent' => $text]);
					$query->byUserAgent($userAgent);
					break;
				case 'ip':
					$query->byIp($text);
					break;
				case 'cookie':
					$query->byCookie((int) $text);
					break;
			}
		}

		$this->template->logins = $this->em->getRepository(Login::class)
			->fetch($query);
	}



	protected function createComponentLoginForm()
	{
		$form = new Form();
		$form->addSelect('type', 'Type', [
			'user' => 'User',
			'ip' => 'IP',
			'cookie' => 'Cookie',
			'userAgent' => 'User agent',
			'fingerprint' => 'Fingerprint hash',
		]);
		$form->addSelect('success', 'Success', [
			'all' => 'All attempts',
			'success' => 'Only successful',
			'failed' => 'Only failed',
		]);
		$form->addText('text', 'Text');
		$form->addSubmit('submit', 'Submit');
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			$this->redirect('this', ['type' => $values->type, 'success' => $values->success, 'text' => $values->text]);
		};
		return $form->setBootstrapRenderer();
	}



	/**
	 * @return \VisualPaginator
	 */
	protected function createComponentVp()
	{
		return new \VisualPaginator();
	}

}
