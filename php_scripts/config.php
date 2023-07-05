<?php
//	define('DB_SERVER', "172.20.10.5");
	define('DB_SERVER', "localhost");
	define('DB_DATABASE', "kod");
	define('DB_USER', "root");
	define('DB_PASSWORD', "root");
	
	$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);