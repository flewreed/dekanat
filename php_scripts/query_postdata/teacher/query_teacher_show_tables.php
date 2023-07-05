<?php

    //query_teacher_show_tables.php
    //подключение файлов
    require '../../session.php';
    require '../../teach_data.php';
    require '../query_helper.php';

    //инициализация переменной
    $semester = 0;

    //инициализация глобальной переменной
    global $con, $perfomance, $stud_list;

    //инициализация массива
    $types = array();

    //вызов функции для получения данных преподавателя
    $teacher = getTeacherData();

    //получение данных из форм
    $disid = $_POST['disid'];
    $group_num = $_POST['group_num'];

    //инициализация переменных
    $teach_id = $teacher['id_teach'];
    $semester = getSemester($group_num);

    //формирование запроса для получения типов занятий
    $qy_types = "SELECT name_type,semester FROM kod.stud_plan WHERE id_teach = $teach_id AND semester = $semester AND id_discipline = $disid AND group_number = '$group_num';";
    $types_qry = mysqli_query($con, $qy_types);
    if(mysqli_num_rows($types_qry) > 0) {
        while($row_types = mysqli_fetch_assoc($types_qry)){
            $types[] = $row_types['name_type'];
            $semester = $row_types['semester'];
        }
    }

    //формирование селекта
    $add_select="";
    $add_select = "<option value=\"choose\">--Выберите тип занятия--</option>";

    for ($i = 0; $i < count($types); $i++) {
        $add_select .= "<option value=\"$types[$i]\">" . $types[$i] . "</option>";
    }

    //вызов функции для получения списка студентов по номеру группы
    getStudList($group_num);

    //вызов функции для получения успеаемости студентов
    getStudPerform($teach_id, $disid, $semester, $group_num);

    //ТАБЛИЦА 1

    $table1 = "";
    if (count($stud_list) > 0) {
        $table1 =   "<tr>
                        <th width=\"70%\"><h4>ФИО студента</h4></th>
                        <th><h4>Балл</h4></th>
                    </tr>";
    }

    for ($i = 0; $i < count($stud_list); $i++) {
        $table1 .= "<tr>
                        <td><h4 id=\"studentname\">";
        if(!is_null($stud_list[$i]['patronymic_stud'])){
            $table1 .= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud']." ".$stud_list[$i]['patronymic_stud'];
        } else {
            $table1.= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud'];
        }

        $table1.=       "</h4></td>
                        <td><input class='studentsmark1' type=\"number\" id=\"studentsmark\" step=\"0.1\" min=\"-1\" max=\"100\" value='0'></td>
                    </tr>";
    }

    // ТАБЛИЦА 2
    $table2 = "";
    if (count($stud_list) > 0) {
        $table2 =   "<tr>
                        <th id=\"fixedfio\"><h4>ФИО студента</h4></th>";

        //формирование шапки таблицы
        for ($i = 0; $i < count($perfomance); $i++){
                $table2 .= "<th>
                            <div><input type=\"date\" name=\"calendar\" id=\"editdate\" class='editdate2' value='".$perfomance[$i]['date_cur']."'></div>
                            <div>
                                <select class='edittype2' class=\"lesson\">";
                $class_types = array();
                $class_types[] = $perfomance[$i]['class_type'];
                for ($t = 0; $t < count($types); $t++) {
                    if ($types[$t] !== $class_types[0]) {
                        $class_types[] = $types[$t];
                    }
                }
                for ($t = 0; $t < count($types); $t++) {
                    if (mb_strpos($class_types[$t], "Лек") === 0) {
                        $class_types[$t] = "ЛК";
                    } elseif (mb_strpos($class_types[$t], "Пр") === 0) {
                        $class_types[$t] = "ПР";
                    } elseif (mb_strpos($class_types[$t], "Сем") === 0) {
                        $class_types[$t] = "С";
                    } elseif (mb_strpos($class_types[$t], "Лаб") === 0) {
                        $class_types[$t] = "ЛБ";
                    }

                    $table2 .= 			"<option>".$class_types[$t]."</option>";
                }
                $table2 .=			"</select>
                            </div>
                        </th>";
        }
        $table2 .= "</tr>";


        //заполнение строк - имя студента, его баллы

        for ($k = 0; $k < count($stud_list); $k++) {

            $table2 .= "<tr>
                            <td><h4 id=\"studentname\">";

            if(!is_null($stud_list[$k]['patronymic_stud'])){
                $table2 .= $stud_list[$k]['surname_stud']." ".mb_substr($stud_list[$k]['name_stud'], 0, 1).".".mb_substr($stud_list[$k]['patronymic_stud'], 0, 1).".";
            } else {
                $table2 .= $stud_list[$k]['surname_stud']." ".mb_substr($stud_list[$k]['name_stud'], 0, 1).".";
            }

            $table2 .= 		"</h4></td>";

            for ($i = 0; $i < count($perfomance); $i++) {
                $table2 .= 	"<td>
                            <input class='point_table2' type=\"number\" id=\"studentsmark\" step=\"0.1\" min=\"-1\" max=\"100\" value='".$perfomance[$i]['stud_points'][$k]['points']."'>
                        </td>";
            }

            $table2 .= "</tr>";
        }
    }

    //ТАБЛИЦА 3

    //формирование шапки таблицы
    $table3 = "";
    if (count($stud_list) > 0) {
        $table3 =   "<tr>
                        <th width=\"70%\"><h4>ФИО студента</h4></th>
                        <th width='15%'><h4>Балл</h4></th>
                        <th><h4>Пропуски</h4></th>
                    </tr>";
    }

    //заполнение строк
    for ($i = 0; $i < count($stud_list); $i++) {
        $table3 .= "<tr>
                        <td><h4 id=\"studentname\">";
        if(!is_null($stud_list[$i]['patronymic_stud'])){
            $table3 .= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud']." ".$stud_list[$i]['patronymic_stud'];
        } else {
            $table3.= $stud_list[$i]['surname_stud']." ".$stud_list[$i]['name_stud'];
        }

        $table3.=       "</h4></td>
                        <td><h4 id=\"studentmark\">".$stud_list[$i]['total_points']."</h4></td>
                        <td><h4 id=\"studentmark\">".$stud_list[$i]['passes']."</h4></td>
                    </tr>";
    }

    //формирование json-файла для передачи данных в формы
    echo json_encode(array( 'class_types' => $add_select, 'table1' => $table1, 'table2' => $table2, 'table3' => $table3));