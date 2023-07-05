<?php
	//query_deanery_groups.php
	//подключение файлов
	require '../../config.php';
	
	//получение id факультета и курса из форм
	$subid = $_POST['subid'];
	$course = $_POST['course'];
	
	//инициализация массива групп
	$groups = array();
	
	//вызов функции для заполнение массива группы по факультету и курсу
	getGroups($subid, $course);
	
	//формирование пунктов выпадающего списка групп
	$groups_select = "<option value=\"choose\">--Выберите группу--</option>";
	for ($i = 0; $i < count($groups); $i++) {
		$groups_select .= "<option value=\"".$groups[$i]['group_number']."\">" . $groups[$i]['group_number'] . "</option>";
	}
	
	echo $groups_select;
	
	//функция для заполнения массива групп по id факультета и курсу
	function getGroups ($subid, $course){
		global $con, $groups;
		
		//определение года формирования группы по полученному курсу
		$year = date('Y');
		$popr=0;
		if(date('n')>8){
			$popr = 1;
		}
		$adm_year = $year - $course + $popr;
		
		//запрос для выборки групп
		$groups_qry = "SELECT g.id_group, g.group_number FROM kod.group g
				join kod.depart_major dm on (g.id_major = dm.dpm_id_major)
				join kod.depart_subdivision ds on (dm.dpm_id_depart = ds.ds_id_depart)
				where ds.ds_id_subdivision = $subid
				and addmission_year = $adm_year";
		$groups__result = mysqli_query($con,$groups_qry);
		if(mysqli_num_rows($groups__result) > 0) {
			while($row_groups = mysqli_fetch_assoc($groups__result)){
				$group = array(
					"id_group" => $row_groups['id_group'],
					"group_number" => $row_groups['group_number'],
				);
				$groups[] = $group;
			}
		}
	}

?>