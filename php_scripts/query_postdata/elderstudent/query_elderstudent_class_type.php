<?php

    //query_elderstudent_class_type.php
    //подключение файлов
    require '../../session.php';
	require '../../elderstudent_data.php';
	require '../../groupInfo.php';

    //инициализирование массива
    $less = array();

    //вызов функции для получения данных группы
    getGroupInfo();

    //инициализирование переменных
    $maj=$groupInfo['name_major'];
    $semester = $groupInfo['semester'];

    //получение данных из форм
    $disciplinename = $_POST['disciplinename'];

    //вызов функции для получения списка типов занятия по выбранной дисциплине
    getTypeClass($disciplinename, $semester, $maj);

    //формирование селекта
    echo "<option value=\"choose\">--Выберите тип занятия--</option>";
    for ($i = 0; $i < count($less); $i++) {
        echo "<option value=\"$less[$i]\">".$less[$i]."</option>";
    }

    //функция для получения списка типов занятия
    function getTypeClass($dis,$semester, $maj){
        global $con, $less;
        $getTypeClass = mysqli_query($con,"SELECT name_type FROM kod.class_type where id_class_type in (SELECT dh_class_type_id FROM kod.discipline_class_type where hours!=0 and dh_id_sem_dis in (SELECT sd_id_sem_dis FROM kod.semester_discipline where sd_semester = $semester and sd_id_dis_maj in (SELECT id_dis_maj FROM kod.discipline_major where dm_id_discipline in (SELECT id_discipline FROM kod.discipline where name_discipline LIKE '$dis') and dm_id_major in (SELECT id_major FROM kod.major where name_major LIKE '$maj'))))");
        if(mysqli_num_rows($getTypeClass) > 0) {
            while($row_typeclass = mysqli_fetch_assoc($getTypeClass)){
                $less[] = $row_typeclass['name_type'];
            }
        }
    }