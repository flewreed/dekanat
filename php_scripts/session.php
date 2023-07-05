<?php

	//session.php
	//вызов функции для начала сессии
	session_start();
	$_SESSION['login'] = null;
	$_SESSION['password'] = null;
	function logout() {
		// сбрасываем сессию
		$_SESSION['isLogged'] = false;
		$_SESSION['login'] = null;
		$_SESSION['password'] = null;
		$_SESSION['user_access'] = null;
		$_SESSION['$user_id'] = null;

		unset($_SESSION['login']);
		unset($_SESSION['password']);
		unset($_SESSION['isLogged']);
		unset($_SESSION['user_access']);
		unset($_SESSION['$user_id']);
		$_SESSION = array();
		session_unset();
		session_destroy();


		// выход на стартовую страницу авторизации
		echo '<script>window.location.href = "index.php";</script>';
		exit();
	}

	//функция начала сессии
	function login(){
		// проверяем наличие сессии
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || !isset($_SESSION['isLogged'])) {
			// показываем окно авторизации
			$_SESSION['isLogged'] = true;
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');

			// алерт при отмене ввода данных
			echo 	"<script>
						alert('Введите логин и пароль');
						window.location.href = 'index.php';
					</script>";
			exit();
		} else {
			// запоминаем сессию, очищаем сервер
			$_SESSION['isLogged'] = true;
			$_SESSION['login'] = $_SERVER['PHP_AUTH_USER'];
			$_SESSION['password'] = $_SERVER['PHP_AUTH_PW'];
			$_SERVER['PHP_AUTH_USER'] = "";
			$_SERVER['PHP_AUTH_PW'] = "";
			$_SERVER['PHP_AUTH_USER'] = null;
			$_SERVER['PHP_AUTH_PW'] = null;
			unset($_SERVER['PHP_AUTH_USER']);
			unset($_SERVER['PHP_AUTH_PW']);


			// подключение к БД
			require 'config.php';
			if(!@$con){
				// алерт при сбое подключения
				echo 	"<script>
							alert('Повторите попытку позже');
							logout();
						 </script>";
				exit;
			} else {
				// проводим проверку введенных данных
				require 'user_check.php';

				// определяем, что показывать пользователю
				switch($_SESSION['user_access']){
					// студент
					case 1:
						echo '<script>window.location.href = "student.php";</script>';
						exit();

					// староста
					case 2:
						echo "<style> #menu3 {display: none;}
								#menu4 {display: none;}
								#menu5 {display: none;}
								#menu6 {display: none;} </style>";
						break;

					// преподаватель
					case 3:
						echo '<script>location.replace("teacher.php");</script>';
						exit();

					// деканат
					case 4:
						echo "<style> #menu1 {display: none;}
								#menu2 {display: none;}
								#menu3 {display: none;}
								
								#menu6 {display: none;}</style>";
						break;

					// администратор
					case 5:
						echo "<style> #menu1 {display: none;}
								#menu2 {display: none;}
								#menu3 {display: none;}
								#menu4 {display: none;}
								#menu5 {display: none;}
								</style>";
						break;

					// преподаватель/деканат
					case 6:
						echo "<style> #menu1 {display: none;}
								#menu2 {display: none;}
								#menu6 {display: none;}</style>";
						break;
				}
			}
		}
	}

	//функция для выхода из сессии
	if (isset($_GET['logout']))
	{
		logout();
	}
?>
