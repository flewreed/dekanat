<?php
//	require 'php_scripts/auth.php';
//	require 'php_scripts/user_check.php';
	require 'php_scripts/session.php';
	login();

//	echo $login;
//	print_r($_SESSION['user']);
//	print_r($_SESSION['password']);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style/style.css">
	<link rel="stylesheet" type="text/css" href="style/media.css">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400&display=swap" rel="stylesheet">
	<title>Балльно-рейтинговая система</title>
</head>
<body>
<div id="header">
	<img src="images/logo.png" alt="Логотип" width="130px"/>
	<h1><b>Балльно-рейтинговая система</b></h1>
	<h3>Санкт-Петербургский государственный технологический институт</h3>

<!--	Изменила кнопку немного-->
	<a href="?logout">
		<Button id="exitbutton"><h3>Выйти</h3></Button>
	</a>
	
	<div class="topmenu">
		<div id="menu1" class="menu">
			<a href="student.php">	&emsp;Студенту&emsp;</a>
		</div>

		<div id="menu2" class="menu">
			<a href="elderstudent.php">&emsp;Старосте&emsp;</a>
		</div>

		<div id="menu3" class="menu">
			<a href="teacher.php">&emsp;Преподавателю&emsp;</a>
		</div>

		<div id="menu4" class="menu">
			<a href="deanery.php">&emsp;Деканату&emsp;</a>
		</div>

		<div id="menu5" class="menu">
			<a href="guide.php">&emsp;Справочник&emsp;</a>
		</div>

		<div id="menu6" class="menu">
			<a href="admin.php">&emsp;Администратору&emsp;</a>
		</div>

	</div>
</div>

<div class="container_slider">
	<img class="photo_slider" src="https://technolog.edu.ru/public/upload/gallery/1/278717621615193b416d30_big.jpg" alt="">
	<img class="photo_slider" src="https://technolog.edu.ru/public/upload/gallery/1/7794037296151938108f2d_big.jpg" alt="">
	<img class="photo_slider" src="https://technolog.edu.ru/public/upload/gallery/1/2061375984615193d5e8363_big.jpg" alt="">
	<img class="photo_slider" src="https://technolog.edu.ru/public/upload/gallery/1/58475142615193676d5be_big.jpg" alt="">
</div>

<div id="footer">
	<p> &copy; 2022, Факультет информационных технологий и управления,<br>
		Санкт-Петербургский государственный технологический институт<br>
		(Технический университет)</p>
</div>

</body>
</html>