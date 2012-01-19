<?php
	// base.inc.php

	include('includes/S3.php');
	define('AWS_ACCESS','');
	define('AWS_SECRET','');
	define('AWS_BUCKET',''); 
	define('DIR_PATH','plugins'); // directory containing plugins
	
	S3::setAuth(AWS_ACCESS,AWS_SECRET);
	

?>