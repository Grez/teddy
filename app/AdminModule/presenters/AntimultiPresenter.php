<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Game\Entities\Bans\Ban;
use Teddy\Entities\Bans\Bans;
use Teddy\Entities\Logs\BansQuery;
use Game\Entities\Logs\UserLog;
use Game\Entities\User\Login;
use Teddy\Entities\User\LoginListQuery;
use Game\Entities\User\User;
use Game\Entities\User\UserAgent;
use Teddy\Entities\User\UserListQuery;
use Teddy\Forms\Form;



/**
 * @TODO: kontrolor, zahraničí, thor hammer
 */
class AntimultiPresenter extends BasePresenter
{

	/** @var Bans @inject */
	public $bans;



	public function renderNewUsers()
	{
		$this->template->users = $this->em->getRepository(User::class)
			->fetch((new UserListQuery())->orderByRegistration())
			->applyPaginator($this['visualPaginator']->getPaginator());
	}



	public function renderBans()
	{
		$query = (new BansQuery());
		$bans = $this->bans->fetch($query);
		$bans->applyPaginator($this['visualPaginator']->getPaginator(), 20);
		$this->template->bans = $bans;
	}



	/**
	 * @param string $type
	 * @param string $success
	 * @param string $text
	 */
	public function renderDefault($type, $success, $text)
	{
		$query = (new LoginListQuery());

		if ($success) {
			$this['loginForm']['success']->setDefaultValue($success);
			if ($success === 'success') {
				$query->onlySuccessful();
			}

			if ($success === 'failed') {
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
				case 'fingerprint':
					$query->byFingerprint($text);
					break;
			}
		}

		$this->template->logins = $this->em->getRepository(\Game\Entities\User\Login::class)
			->fetch($query)
			->applyPaginator($this['visualPaginator']->getPaginator());
	}



	public function handleDeleteBan($id)
	{
		$ban = $this->bans->find($id);
		$this->bans->delete($ban);
		$this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_UNBAN_IP, [$ban->getIp(), $ban->getReason()]);
		$this->flashMessage('Ban has been deleted', 'success');
		$this->redirect('bans');
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



	protected function createComponentIpBanForm()
	{
		$form = new Form();
		$form->addText('reason', 'Reason')
			->setRequired();
		$form->addSelect('type', 'Type', [
			Ban::REGISTRATION => 'Registration (user may play from this IP but can\'t register new profiles)',
			Ban::GAME => 'Game (default)',
			Ban::TOTAL => 'Total (DoS attacks etc., return 403 error for request)',
		])->setDefaultValue(Ban::GAME);
		$form->addText('days', 'Days')
			->addCondition(Form::NUMERIC);
		$form->addText('ip', 'IP')
			->setRequired()
			->setAttribute('placeholder', '143.12.123.123, or 143.12.123.*');
		$form->addSubmit('send', 'Ban');
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			$days = (($values['days'] > 0) ? $values['days'] : '∞');
			$this->bans->ban($values['ip'], $values['reason'], $values['days'], $values['type']);
			$this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_BAN_IP, [$values['ip'], $days, $values['reason']]);
			$this->flashMessage('Ban has been created', 'success');
			$this->redirect('this');
		};
		return $form;
	}

}
