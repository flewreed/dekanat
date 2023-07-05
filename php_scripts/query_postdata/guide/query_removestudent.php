<?php
	//query_removestudent.php
	//подключение файлов
	require '../../guide_data.php';
	
	//получение id выбранного студента
	$id_stud = $_POST['id_stud'];
	
	//вызов функций для удалений записей о студенте из бд
	removePerformance();
	removeStudGroup();
	removeAddress();
	removeParents();
	removeStudent();
	
	//возвращение сообщение об успешном удалении студента и сформированной заново вкладке студентов
	echo json_encode(array('msg' => "Студент удален", 'new_table' => refreshTable()));
	
	//функция для удаления записей об успеваемости
	function removePerformance() {
		global $con, $id_stud;
		$perform_qry = "DELETE FROM kod.stud_performance WHERE (sp_id_stud = $id_stud);";
		mysqli_query($con, $perform_qry);
	}
	
	//функция для удаления связи группы и студента
	function removeStudGroup() {
		global $con, $id_stud;
		$group_qry = "DELETE FROM kod.group_stud WHERE (gs_stud_id = $id_stud);";
		mysqli_query($con, $group_qry);
	}
	
	//функция для удаления адресов
	function removeAddress() {
		global $con, $id_stud;
		
		//получение массива адресов студента
		$address = getAddressByStudId($id_stud);
		
		//запрос для удаления связи адреса и студента
		$studaddr_qry = "DELETE FROM kod.stud_address WHERE (sa_id_stud = $id_stud);";
		mysqli_query($con, $studaddr_qry);
		
		for ($i = 1; $i < count($address) + 1; $i++) {
			//запрос для удаления адреса
			$addr_qry = "DELETE FROM kod.address WHERE (id_address = ".$address[$i]['id_address'].");";
			mysqli_query($con, $addr_qry);
		}
	}
	
	//функция для удаления записей о родителях
	function removeParents() {
		global $con, $id_stud;
		
		//получение массива родителей студента
		$parents = getParentsByStudId($id_stud);

		if(count($parents) > 0) {
			//запрос для удаления связи студента и родителя
			$studpar_qry = "DELETE FROM kod.parent_stud WHERE (id_ps_stud = $id_stud);";
			mysqli_query($con, $studpar_qry);
		}
		
		for ($i = 0; $i < count($parents); $i++) {
			//запрос для удаления записи о родителе
			$par_qry = "DELETE FROM kod.parent WHERE (id_parent = ".$parents[$i]['id_parent'].");";
			mysqli_query($con, $par_qry);
		}
	}
	
	//функция для удаления записей о студенте
	function removeStudent() {
		global $con, $id_stud;
		
		//инициализация id пользователя
		$id_user = findUser($id_stud);
		
		//запрос для удаления сутдента
		$stud_qry = "DELETE FROM kod.stud WHERE (id_stud = $id_stud);";
		mysqli_query($con, $stud_qry);
		
		//запрос для удаления пользователя
		$user_qry = "DELETE FROM kod.user WHERE (id_user = $id_user);";
		mysqli_query($con, $user_qry);
	}

	//функция для получения id пользователя
	function findUser($id_stud) {
		global $con;
		$user_qry = "SELECT * FROM kod.stud where id_stud = $id_stud;";
		$user_result = mysqli_query($con, $user_qry);
		$id_user = 0;
		if(mysqli_num_rows($user_result) > 0) {
			$row_stud = mysqli_fetch_assoc($user_result);
			$id_user = $row_stud['s_id_user'];
		}
		return $id_user;
	}
	
	//функция для формирования вкладки Студенты страницы Справочник
	function refreshTable() {
		$students = getStudents();
		$table = "";
		for ($i = 0; $i < count($students); $i++) {
			$table .= 	"<tr class=\"student\">
							<td class=\"student\">";
			$table .=		 	"<form action=\"editstudent.php\" method=\"post\" class='edit1'>
								<a href='editstudent.php' class='edit1'><button id=\"editstudent\" name='stud_id' value='".$students[$i]['id_stud']."'>Редактировать</button></a>
							</form>";
			$table .= 			"<img src=".$students[$i]['photo_stud']." id=\"studentphoto\">";
			if(!is_null($students[$i]['patronymic_stud'])){
				$table .= 		"<h3 id=\"studentname\">".$students[$i]['surname_stud']." ".$students[$i]['name_stud']." ".$students[$i]['patronymic_stud']."</h3>";
			} else {
				$table .= 		"<h3 id=\"studentname\">".$students[$i]['surname_stud']." ".$students[$i]['name_stud']."</h3>";
			}
			$table .=            "<button id=\"deletestudent\" class='edit1 remove' value='".$students[$i]['id_stud']."'>Удалить</button>";
			$table .= 			"<h4>Направление: </h4>";
			$table .=			"<p id=\"major\">".$students[$i]['code_major']." ".$students[$i]['name_major']."</p>";
			$table .=			"<h4>Номер группы: </h4>";
			$table .=			"<p id=\"group\">".$students[$i]['group_number']."</p>";
			$table .=			"<h4>Номер зачетной книжки: </h4>";
			$table .=			"<p id=\"gradebook\">".$students[$i]['gradebook_number']."</p>";
			$table .=			"<h4>Гражданство: </h4>";
			$table .=			"<p id=\"country\">".$students[$i]['country']."</p>";
			$table .=			"<h4>Форма обучения: </h4>";
			$table .=			"<p id=\"educationform\">".$students[$i]['education_stage']." (".$students[$i]['education_form'].", ".$students[$i]['group_year'].")</p>";
			$table .=			"<h4>Тип финансирования: </h4>";
			$table .=			"<p id=\"financingtype\">".$students[$i]['education_basis']." (".$students[$i]['addmission_type'].")</p>";
			$table .=			"<h4>Контактные данные: </h4>";
			
			$count_cont = 0;
			if (!is_null($students[$i]['phone'])) {
				$phone = "8 (".mb_substr($students[$i]['phone'], 0, 3).") "
					.mb_substr($students[$i]['phone'], 3, 3)."-"
					.mb_substr($students[$i]['phone'], 6,2)."-"
					.mb_substr($students[$i]['phone'], 8);
				if (!is_null($students[$i]['mail'])) {
					$table .= 	"<p class=\"studentphone contact\">".$phone.",</p>";
				} else {
					$table .= 	"<p class=\"studentphone contact\">".$phone."</p>";
				}
				$count_cont++;
			}
			if (!is_null($students[$i]['mail'])) {
				$table .=		"<p class=\"studentemail contact\">".$students[$i]['mail']."</p>";
				$count_cont++;
			}
			if ($count_cont === 0) {
				$table .= 		"<p class=\"teacherphone contact\">не указаны</p>";
			}
			$table .= 		"</td>
                		</tr>";
		}
		return $table;
	}
?>