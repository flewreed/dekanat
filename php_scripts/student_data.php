<?php

    //student_data.php
    //подключение файлов
	require 'config.php';

	// массив данных студента
	$stud = array();

    //функция для получения информации о студенте
	function personalInfo(){
		// запрос поиска студента по id пользователя
		global $stud, $semester, $con;

		$stud_qry = "select*from kod.stud_data where s_id_user=".$_SESSION['$user_id'].";";
		$stud_result = mysqli_query($con, $stud_qry);
		if(mysqli_num_rows($stud_result) > 0) {

			$row_stud = mysqli_fetch_assoc($stud_result);
			$stud = array(
				"surname_stud" => $row_stud['surname_stud'],
				"name_stud" => $row_stud['name_stud'],
				"patronymic_stud" => $row_stud['patronymic_stud'],
				"code_major" => $row_stud['code_major'],
				"name_major" => $row_stud['name_major'],
				"group_number" => $row_stud['group_number'],
				"gradebook_number" => $row_stud['gradebook_number'],
				"flow_number" => $row_stud['flow_number'],
                "gender_stud" => $row_stud['gender_stud'],
			);

			// определяем фото студента по полу
			$path_image = "images/";
			if ($row_stud['photo_stud'] === 'default.png') {
				if ($row_stud['gender_stud'] === 'м') {
					$stud['photo_stud'] = $path_image.'default_m.png';
				} else {
					$stud['photo_stud'] = $path_image.'default_f.png';
				}
			} else $stud['photo_stud'] = $path_image.$row_stud['photo_stud'];

			$month = date('n');

			$semester = (date('Y') - $row_stud['group_year'])*2;
			if ($month>8){
				$semester++;
			}
		} else {

			// !!! Пользователь имеет либо не те права в таблице kod.access, либо отсутствует таблице kod.stud !!!
			// !!! Указать почту для обращения при ошибке !!!

			echo 	"<script>
						alert('Данного студента не существует. Обратитесь к администратору сайта: mail.ru');
				 </script>";
			logout();
			exit;
		}
	}

	//УСПЕВАЕМОСТЬ СТУДЕНТА

	//ПО ДИСЦИПЛИНАМ
	function getAttestation($semester) {
		global $stud, $con;
		require 'groupInfo.php';

		$attestation = array();
		$disciplinies = getDisciplinies($semester, $stud['group_number']);
		$attestation = $disciplinies;

		for($i = 0; $i < count($attestation); $i++) {
			if (!array_key_exists("points", $attestation[$i])) {
				$attestation[$i]['points'] = 0;
				$attestation[$i]['passes'] = 0;
			}
		}

		$attestation_qry = "select*from kod.stud_attestation where s_id_user=".$_SESSION['$user_id']." AND sd_semester=$semester;";
		$attestation_result = mysqli_query($con, $attestation_qry);

		if(mysqli_num_rows($attestation_result) > 0) {
			while ($row_attestation = mysqli_fetch_assoc($attestation_result)) {
				for ($i = 0; $i < count($attestation); $i++) {
					if ($attestation[$i]["name_discipline"] === $row_attestation["name_discipline"]
						&& $attestation[$i]["name_attestation"] === $row_attestation["name_attestation"]) {
						$attestation[$i]["points"] = $row_attestation['total_points'];
						$attestation[$i]["passes"] = $row_attestation['passes'];
					}
				}
			}
		}
		getSemesterGroupRating($stud['group_number'], $semester);

		return $attestation;
	}


	//ПО ЗАНЯТИЯМ
	function getPerform($semester) {
		global $con;
		$perform = array();

		$stud_qry = "select*from kod.stud_perform where s_id_user=".$_SESSION['$user_id']." AND sd_semester=$semester;";
		$stud_result = mysqli_query($con, $stud_qry);

		if(mysqli_num_rows($stud_result) > 0) {
			while ($row_stud = mysqli_fetch_assoc($stud_result)) {
				$perform_line = array(
					"name_discipline" => $row_stud['name_discipline'],
					"name_attestation" => $row_stud['name_attestation'],
					"class_type" => $row_stud['name_type'],
					"date_perform" => $row_stud['date_perform'],
				);

				if ($row_stud["points"] >= 0) {
					$perform_line['points'] = $row_stud["points"];
				} else {
					$perform_line['points'] = 0;
				}

				$perform[] = $perform_line;
			}
		}

		return $perform;
	}