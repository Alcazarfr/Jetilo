<?php

include ROOT_PATH.'includes/configuration.php';
include ROOT_PATH.'includes/fonctions.php';
include ROOT_PATH.'includes/messages.php';
	
function init()
{	
	session_start();
	
   	header('Content-Type: text/html; charset=UTF-8');
	
	loadConfiguration();
}

init();

?>