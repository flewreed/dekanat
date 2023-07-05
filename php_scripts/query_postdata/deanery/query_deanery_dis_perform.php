<?php
	//query_deanery_perform.php
	//подключение файлов
	require '../../config.php';
	require '../query_helper.php';
	
	//получение данных из форм
	$dis_id = $_POST['discipline_id'];
	$group_number = $_POST['group_number'];
	
	//определение текущего семестра для группы
	$semester = getSemester($group_number);
	
	//получение массива студентов по номеру группы
	getStudList($group_number);
	global $stud_list;
	
	//инициализация массива успеваемости студентов по id дисциплины, семестру и номеру группы
	$perfomance = getStudPerformance($dis_id, $semester, $group_number);
	
	//формирование таблицы успеваемости группы по выбранной дисциплине
	$table = "";
	if (count($stud_list) > 0) {
		$table =   "<tr>
                    	<th id=\"fixedfio\"><h4>ФИО студента</h4></th>";
		
		for ($i = 0; $i < count($perfomance); $i++){
            $table .= "<th>
                        <h4 id=\"lessdate\">".date_format(date_create($perfomance[$i]['date_cur']), 'd.m')."</h4>";
            $class_type = $perfomance[$i]['class_type'];
            if (mb_strpos($class_type, "Лек") === 0) {
                $class_type = "ЛК";
            } elseif (mb_strpos($class_type, "Пр") === 0) {
                $class_type = "ПР";
            } elseif (mb_strpos($class_type, "Сем") === 0) {
                $class_type = "С";
            } elseif (mb_strpos($class_type, "Лаб") === 0) {
                $class_type = "ЛБ";
            }
            $table .=		"<h4 id=\"lesstype\">".$class_type."</h4>
                    </th>";
		}
		$table .= "</tr>";
		
		for ($k = 0; $k < count($stud_list); $k++) {
			
			$table .= "<tr>
                            <td><h4 id=\"studentname\">";
			
			if(!is_null($stud_list[$k]['patronymic_stud'])){
				$table .= $stud_list[$k]['surname_stud']." ".mb_substr($stud_list[$k]['name_stud'], 0, 1).".".mb_substr($stud_list[$k]['patronymic_stud'], 0, 1).".";
			} else {
				$table .= $stud_list[$k]['surname_stud']." ".mb_substr($stud_list[$k]['name_stud'], 0, 1).".";
			}
			
			$table .= 		"</h4></td>";
			
			for ($i = 0; $i < count($perfomance); $i++) {
                $table .= 	"<td>
                            <h4 id=\"misscount\">".$perfomance[$i]['stud_points'][$k]['points']."</h4>
                        </td>";
			}
			
			$table .= "</tr>";
		}
	}
	
	//возвращение сформированной таблицы на страницу
	echo $table;
	
	//функция для создания массива успеваемости
	function getStudPerformance($disid, $semester, $group_number) {
		global $con, $stud_list;
		
		//инициализируем массив
		$perfomance = array();
		
		//запрос для выборки расписания группы по дисциплине
		$perform_qry = "select cur.id_cur, cur.date_cur, ct.name_type
                            from kod.curriculum cur
                            join kod.discipline_class_type dc ON (cur.id_dis_class_cur = dc.id_dis_class)
                            join kod.class_type ct ON (dc.dh_class_type_id = ct.id_class_type)
                            join kod.semester_discipline sd on (dc.dh_id_sem_dis = sd.sd_id_sem_dis)
                            join kod.discipline_major dm on (sd.sd_id_dis_maj = dm.id_dis_maj)
                            where sd.sd_semester = $semester
                            and dm.dm_id_discipline = $disid
                          	and cur.cur_group_num like '$group_number'
                            order by cur.date_cur";
		$perform_result = mysqli_query($con, $perform_qry);
		
		if(mysqli_num_rows($perform_result) > 0) {
			while ($row_perform = mysqli_fetch_assoc($perform_result)) {
				$perform = array(
					"id_cur" => $row_perform['id_cur'],
					"date_cur" => $row_perform['date_cur'],
					"class_type" => $row_perform["name_type"],
				);
				
				for ($i = 0; $i < count($stud_list); $i++) {
					//запрос для выборки баллов студента по определенному занятию
					$stud_perform_qry = "SELECT * FROM kod.stud_perform
										where id_stud= ".$stud_list[$i]['id_stud']." and id_cur = ".$perform['id_cur'].";";
					$stud_perform_result = mysqli_query($con, $stud_perform_qry);
					
					if(mysqli_num_rows($stud_perform_result) > 0) {
						$row_stud_perform = mysqli_fetch_assoc($stud_perform_result);
						$perform_line = array(
							"id_stud" => $row_stud_perform["id_stud"],
							"points" => $row_stud_perform["points"],
						);
						
						//отрицательное значение является пропуском занятия
						if ($perform_line['points'] < 0) {
							$perform_line['points'] = "н";
						}
						$perform['stud_points'][] = $perform_line;
					}

				}
				$perfomance[] = $perform;
			}
		}
		return $perfomance;
	}
?>