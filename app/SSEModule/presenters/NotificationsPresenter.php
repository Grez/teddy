<?php

namespace Teddy\SSEModule\Presenters;

use Teddy\Entities\PM\Messages;



class NotificationsPresenter extends BasePresenter
{

	const REFRESH_REQUEST_AFTER = 30;

	/**
	 * @var Messages
	 * @inject
	 */
	public $messagesFacade;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var array
	 */
	protected $messages;



	// If we didn't use a while loop, the browser would essentially do polling
	// every ~3seconds. Using the while, we keep the connection open and only make
	// one request.

	/**
	 * @param string $hash
	 */
	public function renderDefault($hash)
	{
		session_write_close();
		ob_start();
		$this->getHttpResponse()->setContentType('text/event-stream');
		$this->getHttpResponse()->addHeader('Cache-control', 'no-cache');

		while (TRUE) {
			$this->restartRequestIfNecessary();
			$this->createMessages();

			if ($this->createHash() !== $hash) {
				$this->pushNotifications();
			}
			sleep(2);
		}
	}



	public function renderSleep()
	{
		echo $_SERVER['REQUEST_TIME'] . '<br><br>';
		echo time() . '<br>';
		sleep(10);
		echo time() . '<br>';
		exit;
	}



	public function renderPhpinfo()
	{
		phpinfo();
		exit;
	}



	protected function pm()
	{
		$pm = new \stdClass();
		$pm->unreadCount = $this->messagesFacade->getUnreadMessagesCount($this->getUser()->getEntity());
		return $pm;
	}



	protected function forums()
	{
		$forums = new \stdClass();
//		$forums->unread = $this->forums
		return $forums;
	}



	public function createMessages()
	{
		$this->messages = new \stdClass();
		$this->messages->pm = $this->pm();
//		$this->messages->forums = $this->forums();
	}



	protected function createHash()
	{
		$json = json_encode($this->messages);
		return md5($json);
	}



	public function pushNotifications()
	{
		echo "id: " . $_SERVER['REQUEST_TIME'] . PHP_EOL;
//		echo "data: {\n";
		echo "data: " . json_encode($this->messages) . "\n";
//		echo "data: }\n";
		echo PHP_EOL;
		ob_flush();
		flush();
		die();
	}


	/**
	 * We want to restart connection every few seconds
	 * Kills current request (and browser will reopen it if it is still there)
	 */
	protected function restartRequestIfNecessary()
	{
		if ((time() - $_SERVER['REQUEST_TIME']) > self::REFRESH_REQUEST_AFTER) {
			die();
		}
	}

}
