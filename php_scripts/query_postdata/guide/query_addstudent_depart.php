<?php
	//query_addstudent_depart.php
	//подключение файлов
	require '../../guide_data.php';
	
	//получение id факультета из форм
	$sub_id = $_POST['subdivision_id'];
	
	//инициализация массива кафедр и его получение с помощью вызова функции
	$depart = getDepartmentsBySubId($sub_id);

	//формирование выпадающего списка кафедр
	$dep_select = "<option value=\"choose\">--Выберите кафедру--</option>";
	for ($i = 0; $i < count($depart); $i++) {
		$dep_select .= "<option value='".$depart[$i]['id_depart']."'>".$depart[$i]['name_depart']."</option>";
	}
	
	echo $dep_select;
?>