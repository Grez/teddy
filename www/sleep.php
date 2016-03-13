<?php

//ob_start();
session_start();
session_write_close();
//ob_flush();
//flush();
echo $_SERVER['REQUEST_TIME'] . '<br><br>';
echo time() . '<br>';
sleep(5);
echo time() . '<br>';
