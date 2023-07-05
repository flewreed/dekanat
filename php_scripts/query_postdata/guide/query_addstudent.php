<?php
	//query_addstudent.php
	//подключение файлов
	require '../../config.php';
	
	//получение данных из форм в формате json
	$stud_json = $_POST['stud_json'];
	$address_json = $_POST['address_json'];
	$parents_json = $_POST['parents_json'];
	
	$group_id = $_POST['group_id'];
	$group_num = $_POST['group_num'];
	
	//преобразование json в массивы
	$stud = json_decode($stud_json, true);
	$address = json_decode($address_json, true);
	$parents = json_decode($parents_json, true);
	
	//проверка на пустые формы
	if ($stud['surname'] === "" || $stud['name'] === "" || $stud['email'] === ""
		|| $stud['phone'] === "" || $stud['ad_year'] === "choose"
		|| $stud['gradebook_num'] === "" || $stud['date_of_birth'] === "") {
		echo "Данные о студенте не заполнены";
		exit(0);
	}
	
	//проверка корректности указанного телефона
	if (mb_strpos($stud['phone'], "_") !== false){
		echo "Телефон студента указан некорректно";
		exit(0);
	}
	
	//проверка корректности указанного email
	if (filter_var($stud['email'], FILTER_VALIDATE_EMAIL) === false){
		echo "Электронная почта студента указана некорректно";
		exit(0);
	}

	//проверка выбранной группы
	if ($group_id === "choose") {
		echo "Группа не выбрана";
		exit(0);
	}
	
	//проверка на пустые формы
	for ($i = 0; $i < count($address); $i++) {
		if ($address[$i]['country'] === "" || $address[$i]['region'] === "" || $address[$i]['city'] === ""
			|| $address[$i]['street'] === "" || $address[$i]['house'] === "") {
			echo "Адрес не указан";
			exit(0);
		}
	}
	
	//проверка на корректное заполнение данных о родителях
	for ($i = 0; $i < count($parents); $i++) {
		if (($parents[$i]['surname'] !== "" || $parents[$i]['name'] !== "" || $parents[$i]['patronymic'] !== ""
				|| $parents[$i]['job'] !== "" || $parents[$i]['phone'] !== "")
			&& ($parents[$i]['surname'] === "" || $parents[$i]['name'] === "" || $parents[$i]['phone'] === "")) {
			echo "Данные о родителях не указаны полностью";
			exit(0);
		}
		if ($parents[$i]['surname'] !== "") {
			if (mb_strpos($parents[$i]['phone'], "_") !== false){
				echo "Телефоны родителей указаны некорректно";
				exit(0);
			}
		}
	}
	
	//добавление пользователя в бд, получение id
	$id_user = addUser();
	
	//добавление студента в бд, получение id
	$id_stud = addStudent($id_user);
	
	//связывание студента с группой
	addGroup($id_stud, $group_id, $group_num);
	
	//добавление адреса студента
	addAddress($id_stud);
	
	//добавление информации о родителях
	addParents($id_stud);
	
	echo "Данные внесены успешно";
	
	
	//функция для добавления пользователя в бд
	function addUser() {
		global $con;
		
		//генерация логина по фио
		$login = createLogin();
		
		//генерация случайного пароля
		$pass = createRandomPassword();
		
		//запрс на добавление пользователя
		$user_qry = "INSERT INTO kod.user (login, password) VALUES ('".$login."', '".$pass."');";
		mysqli_query($con, $user_qry);
		
		//запрос для получения id созданного пользователя
		$id_qry = "SELECT * FROM kod.user where login like '".$login."';";
		$id_result = mysqli_query($con, $id_qry);
		$id_user = -1;
		if(mysqli_num_rows($id_result) > 0) {
			$row_log = mysqli_fetch_assoc($id_result);
			$id_user = $row_log['id_user'];
		}
		return $id_user;
	}
	
	//функция для добавления студента в бд
	function addStudent($id_user) {
		global $con, $stud;
		
		//запрос для проверки уникальности введенных данных
		$isunique_qry = "SELECT * FROM kod.stud
						where current_mail_stud like '".$stud['email']."'
						or current_phone_stud like '".$stud['phone']."'
						or gradebook_number like '".$stud['gradebook_num']."';";
		$isunique_result = mysqli_query($con, $isunique_qry);
		if(mysqli_num_rows($isunique_result) > 0) {
			echo "Студент с введенными номером телефона, почтой или номером зачетки уже существует";
			//удаление пользователя
			mysqli_query($con, "DELETE FROM kod.user WHERE id_user = $id_user;");
			exit(0);
		}
		
		//запрос для добавление студента
		$stud_qry = "INSERT INTO kod.stud (s_id_user, surname_stud, name_stud, patronymic_stud,
						current_mail_stud, current_phone_stud, id_education_basis, id_addmission_type,
						addmission_year, gradebook_number, gender_stud, date_of_birth)
					VALUES ('".$id_user."', '".$stud['surname']."', '".$stud['name']."', ";
		if ($stud['patronymic'] === "") {
			$stud_qry .= "null,";
		} else{
			$stud_qry .= "'".$stud['patronymic']."',";
		}
		$stud_qry .= " '".$stud['email']."', '".$stud['phone']."', '".$stud['ed_basis_id']."', '".$stud['ad_type_id'].
			"', ".$stud['ad_year'].", '".$stud['gradebook_num']."', '".$stud['gender']."', '".$stud['date_of_birth']."');";
		
		mysqli_query($con, $stud_qry);
		
		//запрос для получения id добавленного студента
		$id_qry = "SELECT * FROM kod.stud where s_id_user = $id_user;";
		$id_result = mysqli_query($con, $id_qry);
		$id_stud = -1;
		if(mysqli_num_rows($id_result) > 0) {
			$row_log = mysqli_fetch_assoc($id_result);
			$id_stud = $row_log['id_stud'];
		}
		return $id_stud;
	}
	
	//функция для связывания группы и студента
	function addGroup($id_stud, $group_id, $group_num) {
		global $con;
		
		//запрос на добавление записи о группе и студенте
		$addgr_qry = "INSERT INTO kod.group_stud (gs_group_id, gs_stud_id) VALUES ($group_id, $id_stud);";
		mysqli_query($con, $addgr_qry);
		
		//вызов функции для добаления успеваемости
		addPoints($id_stud, $group_num);
	}
	
	//функция для добавления успеваемости нового студента
	function addPoints($id_stud, $group_num){
		global $con;
		
		//запрос для поиска id занятий группы
		$cur_qry = "SELECT * FROM kod.curriculum where cur_group_num like '".$group_num."';";
		$cur_result = mysqli_query($con, $cur_qry);
		
		if(mysqli_num_rows($cur_result) > 0) {
			while ($row_cur = mysqli_fetch_assoc($cur_result)) {
				$id_cur = $row_cur['id_cur'];
				
				//запрос на добавление баллов созданному студенту
				mysqli_query($con, "INSERT INTO kod.stud_performance (sp_id_stud, sp_id_cur, points)
										VALUES (".$id_stud.", ".$id_cur.", '0');");
			}
		}
	}
	
	//функция для добавления адреса
	function addAddress($id_stud) {
		global $con, $address;
		
		for ($i = 0; $i < count($address); $i++) {
			//получение id страны
			$address[$i]['id_country'] = getCountryId($address[$i]['country']);
			
			//запрос на добавление записи об адресе
			$addAddr_qry = "INSERT INTO kod.address (id_type_addr, id_addr_country, oblast, city, street, house, flat)
							VALUES ('".$address[$i]['type_address']."', '".$address[$i]['id_country']."',
							'".$address[$i]['region']."', '".$address[$i]['city']."',
							'".$address[$i]['street']."', '".$address[$i]['house']."', ";
			if ($address[$i]['flat'] === "") {
				$addAddr_qry.= "null);";
			} else {
				$addAddr_qry.= "'".$address[$i]['flat']."');";
			}
			mysqli_query($con, $addAddr_qry);
			
			//вызов функции для получения id добавленного адреса
			getAddressId($i);
			
			//запрос для связывания адреса и студента
			mysqli_query($con, "INSERT INTO kod.stud_address (sa_id_addr, sa_id_stud)
 				  					  VALUES (".$address[$i]['id_address'].", ".$id_stud.");");
		}
	}
	
	//функция для ссоздания/получения id страны
	function getCountryId($country){
		global $con;
		
		//запрос для поиска id страны
		$country_qry = "SELECT * FROM kod.country where name_country like '".$country."';";
		$country_result = mysqli_query($con, $country_qry);
		if(mysqli_num_rows($country_result) > 0) {
			$row_country = mysqli_fetch_assoc($country_result);
			$id_country = $row_country['id_country'];
			return $id_country;
		} else {
			//запрос для добавления страны, если такой не было в бд
			mysqli_query($con, "INSERT INTO kod.country (name_country) VALUES ('".$country."');");
			
			//получение id страны
			return getCountryId($country);
		}
	}
	
	//функция для получения id адреса
	function getAddressId($i){
		global $con, $address;
		
		$addrid_qry = "SELECT * FROM kod.address
							where id_type_addr = ".$address[$i]['type_address']."
							and id_addr_country = ".$address[$i]['id_country']. "
							and oblast like '".$address[$i]['region']."'
							and city like '".$address[$i]['city']."'
							and street like '".$address[$i]['street']."'
							and house like '".$address[$i]['house']."'
							and flat ";
		if ($address[$i]['flat'] === "") {
			$addrid_qry.= "is null";
		} else {
			$addrid_qry.= "like '".$address[$i]['flat']."'";
		}
		$addrid_qry.= " ORDER BY id_address DESC LIMIT 1;";
		
		//добавление id адреса в массив
		$addrid_result = mysqli_query($con, $addrid_qry);
		if(mysqli_num_rows($addrid_result) > 0) {
			$row_addr = mysqli_fetch_assoc($addrid_result);
			$address[$i]['id_address'] = $row_addr['id_address'];
		}
	}
	
	//функция для добавления родителей в бд
	function addParents($id_stud) {
		global $con, $parents;
		
		for ($i = 0; $i < count($parents); $i++) {
			if($parents[$i]['surname'] !== "") {
				//запрос для добавления записи
				$addpar_qry = "INSERT INTO kod.parent
							  (surname_parent, name_parent, current_phone_parent, patronymic_parent, job)
							  VALUES ('".$parents[$i]['surname']."', '".$parents[$i]['name']."', '".$parents[$i]['phone']."',";
				
				if ($parents[$i]['patronymic'] !== "") {
					$addpar_qry.= " '".$parents[$i]['patronymic']."', ";
				} else {
					$addpar_qry.= " null, ";
				}
				if ($parents[$i]['job'] !== "") {
					$addpar_qry.= " '".$parents[$i]['job']."');";
				} else {
					$addpar_qry.= " null);";
				}
				
				mysqli_query($con, $addpar_qry);
				
				//вызов функции для получения id родителя
				getParentsId($i);
				
				//запрос для связывания студента и родителя
				mysqli_query($con, "INSERT INTO kod.parent_stud (id_ps_parent, id_ps_stud)
										  VALUES (".$parents[$i]['id_parent'].", ".$id_stud.");");
			}
		}
	}
	
	//функция для получения id родителей
	function getParentsId($i) {
		global $con, $parents;
		
		if($parents[$i]['surname'] !== "") {
			$par_qry = "SELECT * FROM kod.parent
						where surname_parent like '".$parents[$i]['surname']."'
						and name_parent like '".$parents[$i]['name']."'
						and current_phone_parent like '".$parents[$i]['phone']."'";
			
			if ($parents[$i]['patronymic'] !== "") {
				$par_qry.= " and patronymic_parent like '".$parents[$i]['patronymic']."' ";
			} else {
				$par_qry.= " and patronymic_parent is null ";
			}
			if ($parents[$i]['job'] !== "") {
				$par_qry.= " and job like '".$parents[$i]['job']."' ";
			} else {
				$par_qry.= " and job is null ";
			}
			$par_qry.= "order by id_parent DESC LIMIT 1;";
			
			$par_result = mysqli_query($con, $par_qry);
			
			//добавления id родителя в массив
			if(mysqli_num_rows($par_result) > 0) {
				$row_par = mysqli_fetch_assoc($par_result);
				$parents[$i]['id_parent'] = $row_par['id_parent'];
			}
		}
	}
	
	//функция для генерации логина
	function createLogin(){
		global $stud, $con;
		
		//формирование логина на основе транслитерации первых букв фио
		$login = mb_substr(translit($stud['surname']), 0, 1).mb_substr(translit($stud['name']), 0, 1);
		if ($stud['patronymic'] !== "") {
			$login.= mb_substr(translit($stud['patronymic']), 0, 1);
		} else {
			$login.= "_";
		}
		
		//запрос на поиск последней похожей комбинации букв
		$log_qry = "SELECT * FROM kod.user where login like '".$login."%' ORDER BY login DESC LIMIT 1;";
		$log_result = mysqli_query($con, $log_qry);
		
		if(mysqli_num_rows($log_result) > 0) {
			$row_log = mysqli_fetch_assoc($log_result);
			$log = $row_log['login'];
			
			//инициализация индекса как увеличение порядкового номера логина
			$index = mb_substr($log, 3) + 1;
			
			//добавление порядкового номера к сформированному логину
			$login.= $index;
		} else {
			//добавление единицы, если комбинация букв еще не использовалась
			$login.= "1";
		}
		return $login;
	}
	
	//функция для генерации случайного пароля
	function createRandomPassword($length = 10) {
		// $length - длина сгенерированного пароля
		//инициализация строки с возможными символами
		$used_symbols = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$symbols_length = strlen($used_symbols) - 1;
		
		//инициализация пароля
		$pass = '';
		
		for ($i = 0; $i < $length; $i++) {
			//получение индекса символа
			$n = rand(0, $symbols_length);
			
			//добавление символа к паролю по индексу
			$pass .= $used_symbols[$n];
		}
		return $pass;
	}
	
	//функция для транслитирации символов
	function translit($s) {
		$s = (string) $s;
		
		//удаление пробелов в начале и конце строки
		$s = trim($s);
		
		//перевод строки в нижний регистр
		$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
		
		//преобразование символов строки в соответствующие сочетания на английском
		$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
		return $s;
	}
?>