<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

/** @var \Nette\DI\Container $container */
use Teddy\Entities\Bans\Ban;
use Teddy\Entities\Bans\Bans;

$container = require __DIR__ . '/../app/bootstrap.php';
/** @var Bans $bans */
$bans = $container->getByType(Bans::class);
$ban = '';
ob_start();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 */
function sendMsg($id, Ban $ban, $time) {
	echo "id: $id" . PHP_EOL;
	echo "data: {\n";
	echo "data: \"msg\": \"" . $ban->getId() . "\", \n";
	echo "data: \"picovole\": \"" . $time . "\", \n";
	echo "data: \"requesttime\": \"" . $_SERVER['REQUEST_TIME'] . "\", \n";
	echo "data: \"id\": $id\n";
	echo "data: }\n";
	echo PHP_EOL;
	ob_flush();
	flush();
}
$startedAt = time();
do {
	// Cap connections at 30 seconds. The browser will reopen the connection on close
	if ((time() - $startedAt) > 30) {
		die();
	}

	if ($ban !== $bans->getLastBan()) {
		$ban = $bans->getLastBan();
		sendMsg($startedAt, $ban, time());
	}
	sleep(2);
	// If we didn't use a while loop, the browser would essentially do polling
	// every ~3seconds. Using the while, we keep the connection open and only make
	// one request.
} while(true);
