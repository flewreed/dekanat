<?php

    //query_teacher_disciplinies.php
    //подключение файлов
    require '../../session.php';
    require '../../teach_data.php';

    //инициализирование массива
    $groups = array();

    //вызов функции для получение данных преподавателя
    $teacher = getTeacherData();

    //получение данных из форм
    $disid = $_POST['disid'];

    //вызов функции для получения списка групп у данного преподавателя
    getGroups($teacher['id_teach'],$disid);

    //формирование селекта для выбора группы
    echo "<option value=\"choose\">--Выберите группу--</option>";
    for ($i = 0; $i < count($groups); $i++) {
        echo "<option value=\"$groups[$i]\">" . $groups[$i] . "</option>";
    }

    //функция для получения списка групп по номеру преподавателя и названию дисциплины
    function getGroups ($teach_id, $dis){

        global $con, $groups;

        $year = date('Y');
        $popr=0;
        if(date('n')>8){
            $popr = 1;
        }

        $qy = "SELECT group_number FROM kod.stud_plan WHERE id_teach = $teach_id AND semester = ($year - addmission_year)*2 + $popr AND id_discipline = $dis group by group_number;";
        $groups_qry = mysqli_query($con,$qy);
        if(mysqli_num_rows($groups_qry) > 0) {
            while($row_groups = mysqli_fetch_assoc($groups_qry)){
                $groups[] = $row_groups['group_number'];
            }
        }
    }