<?php
	//query_addstudent_group.php
	//подключение файлов
	require '../../guide_data.php';
	
	//получение данных из форм
	$major_id = $_POST['major_id'];
	$ed_form = $_POST['ed_form'];
	$course = $_POST['course'];
	
	//инициализация массива групп и его получение с помощью вызова функции
	$groups = getGroups($major_id, $ed_form, $course);
	
	//формирование выпадающего списка групп
	$gr_select = "<option value=\"choose\">--Выберите группу--</option>";
	for ($i = 0; $i < count($groups); $i++) {
		$gr_select .= "<option value='".$groups[$i]['id_group']."'>".$groups[$i]['group_number']."</option>";
	}
	
	echo $gr_select;
?>