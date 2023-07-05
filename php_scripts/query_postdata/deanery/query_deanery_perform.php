<?php
	//query_deanery_perform.php
	//подключение файлов
	require '../../config.php';
	require '../query_helper.php';
	
	//получение id факультета, курса и номера группы из форм
	$subid = $_POST['subid'];
	$course = $_POST['course'];
	$group_number = $_POST['group_number'];
	
	//определение текущего семестра для группы
	$semester = getSemester($group_number);
	
	//определение списка дисциплин группы в текущем семестре
	$disciplinies = getDisciplinies($semester, $group_number);
	
	//инициализация массива студентов и их успеваемости
	$stud_list = getSemesterGroupRating($group_number, $semester);
	$stud_list = getPerformanse($stud_list, $disciplinies, $semester);
	
	
	
	//формирование списка дисциплин
	$dis_select = "<option value=\"choose\">--Выберите тип занятия--</option>";
	for ($i = 0; $i < count($disciplinies); $i++) {
		$dis_select .= "<option value=".$disciplinies[$i]['id_discipline'].">".$disciplinies[$i]['name_discipline']."</option>";
	}
	
	
	//формирование таблицы ведомости группы за текущий семестр
	$pr_table = 	"<tr>
                        <th id=\"fixedfio\"><h4>ФИО студента</h4></th>";
	for ($i = 0; $i < count($disciplinies); $i++) {
		$pr_table .= 	"<th>
                            <h4 id=\"discipname\">".$disciplinies[$i]['name_discipline']."</h4>
                        </th>";
	}
	$pr_table .= 		"<th>
                            <h4 id=\"totalrating\">Рейтинг/Место</h4>
                        </th>
                    </tr>";
	for ($i = 0; $i < count($stud_list); $i++) {
		$pr_table .= "<tr>
						<td><h4 id=\"studentname\">";
		if(!is_null($stud_list[$i]['patronymic_stud'])){
			$pr_table .= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud']." ".$stud_list[$i]['patronymic_stud'];
		} else {
			$pr_table.= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud'];
		}
		$pr_table .= 	"</h4></td>";
		
		for ($k = 0; $k < count($disciplinies); $k++) {
			$pr_table .= "<td>
                            <h4 id=\"misscount\" title='баллы/пропуски'>".$stud_list[$i]['dis_points'][$k]['total_points']."/".$stud_list[$i]['dis_points'][$k]['total_passes']."</h4>
                        </td>";
		}
		
		$pr_table .= 	"<td>
                            <h4 id=\"misscount\">".$stud_list[$i]['sum_points']."/".$stud_list[$i]['rating']."</h4>
                        </td>
                    </tr>";
		
	}
	
	echo json_encode(array('disciplinies' => $dis_select, 'pr_table' => $pr_table));
	
	//функция для получения успеваемости студентов по id дисциплины и семестру
	function getPerformanse($stud_list, $disciplinies, $semester){
		global $con;
		
		for ($i = 0; $i < count($stud_list); $i++) {
			$stud_list[$i]['dis_points'] = array();
			for ($k = 0; $k < count($disciplinies); $k++) {
				$dis_perform = array(
					"total_points" => 0,
					"total_passes" => 0,
				);
				
				$stud_list_qry = "SELECT * FROM kod.stud_attestation
								  where id_stud = ".$stud_list[$i]['id_stud']."
								  and name_discipline like '".$disciplinies[$k]['name_discipline']."'
								  and sd_semester = $semester;";
				$stud_list_result = mysqli_query($con, $stud_list_qry);
				if(mysqli_num_rows($stud_list_result) > 0) {
					$row_stud_list = mysqli_fetch_assoc($stud_list_result);
					$dis_perform['total_points'] = $row_stud_list['total_points'];
					$dis_perform['total_passes'] = $row_stud_list['passes'];
				}
				
				$stud_list[$i]['dis_points'][] = $dis_perform;
				
			}
		}
		return $stud_list;
	}


?>