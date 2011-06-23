<?php

include ROOT_PATH.'includes/configuration.php';
include ROOT_PATH.'includes/fonctions.php';
	
function init()
{	
	session_start();
	
	loadConfiguration();
}

?>