<?php

header( 'Content-type: text/html; charset=utf-8' );
for($i=0;$i<10;$i++)
{
	echo 'printing...<br>';
	ob_flush();
	flush();
	sleep(1);
}

