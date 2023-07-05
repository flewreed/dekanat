<?php
	//query_addstudent_major.php
	//подключение файлов
	require '../../guide_data.php';
	
	//получение id кафедры из форм
	$dep_id = $_POST['depart_id'];
	
	//инициализация массива направлений и его получение с помощью вызова функции
	$major = getMajorByDepId($dep_id);
	
	//инициализация ступени образвания
	$stage;
	
	//формирование выпадающего списка направлений по ступени образования
	$maj_select = "<option value=\"choose\">--Выберите направление--</option>";
	for ($i = 0; $i < count($major); $i++) {
		if (is_null($stage)) {
			$stage = $major[$i]['name_stage'];
			$maj_select .= "<optgroup label=\"".$stage."\">";
		} else {
			if ($major[$i]['name_stage'] !== $stage) {
				$stage = $major[$i]['name_stage'];
				$maj_select .= "<optgroup label=\"".$stage."\">";
			}
		}
		$maj_select .= "<option value='".$major[$i]['id_major']."'>"
			.$major[$i]['code_major']." ".$major[$i]['name_major']."</option>";
	}
	echo $maj_select;
?>