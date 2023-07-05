<?php

    //query_teacher_edit_table2.php
    //подключение файлов
    require '../../session.php';
    require '../query_helper.php';
    require '../../teach_data.php';

    //вызов функции для получения данных преподавателя
    $teacher = getTeacherData();

    //инициализирование массивов
    $dates = array();
    $types = array();
    $points = array();

    //получение данных из форм
    $disid = $_POST['disid'];
    $group_num = $_POST['group_num'];
    $dates = $_POST['dates'];
    $types = $_POST['types'];
    $points = $_POST['points'];

    //вызов функции для получения списка студентов по номеру группы
    getStudList($group_num);

    //инициализация глобальных переменных
    global $stud_list, $perfomance;

    //инициализация переменных
    $semester = getSemester($group_num);
    $edit_points = "";
    $teach_id = $teacher['id_teach'];

    //переписывания массива с названиями типов занятий
    for ($t = 0; $t < count($types); $t++) {
        if (mb_strpos($types[$t], "ЛК") === 0) {
            $types[$t] = "Лекция";
        } elseif (mb_strpos($types[$t], "ПР") === 0) {
            $types[$t] = "Практика";
        } elseif (mb_strpos($types[$t], "С") === 0) {
            $types[$t] = "Семинар";
        } elseif (mb_strpos($types[$t], "ЛБ") === 0) {
            $types[$t] = "Лабораторная работа";
        }
    }

    //вызов функции для получения списка занятий по номеру преподавателя, номеру дисциплины, семестру и номеру группы
    getStudPerform($teach_id, $disid, $semester, $group_num);

    //вызов функции для обновления данных в таблице учебного плана
    compareData($dates, $perfomance);

    //формирование запросов для изменения типа занятия и баллов студентов
    for($i=0; $i<count($stud_list); $i++){
        for($j=0; $j<count($dates); $j++){

            global $con, $perfomance;

            $disclass = getDisClass($disid, $group_num, $types[$j], $semester);
            $curid = $perfomance[$j]['id_cur'];
            if($types[$j] != $perfomance[$j]['class_type']){
                $qry = "UPDATE kod.curriculum SET id_dis_class_cur = $disclass where id_cur = $curid;";
                mysqli_query($con, $qry);
            }
            $edit_points = "UPDATE kod.stud_performance SET points =".$points[$i*count($dates)+$j]." where sp_id_stud=".$stud_list[$i]['id_stud']." and sp_id_cur=$curid; ";
            mysqli_query($con, $edit_points);
        }
    }

    //функция для получения id_disclass из бд по номеру дисциплины, номеру группы, типу занятия и номеру семестра
    function getDisClass($disid, $group_num, $typeoflesson, $semester){
        global $con;

        $getDisClass_qry = mysqli_query($con,"SELECT id_dis_class FROM kod.discipline_class_type where dh_id_sem_dis in (SELECT sd_id_sem_dis FROM kod.semester_discipline where sd_id_dis_maj in (SELECT id_dis_maj FROM kod.discipline_major where dm_id_discipline = $disid and dm_id_major in (SELECT id_major FROM kod.`group` where group_number like '$group_num')) and sd_semester=$semester) and dh_class_type_id in (SELECT id_class_type FROM kod.class_type where name_type like '$typeoflesson');");
        if(mysqli_num_rows($getDisClass_qry) > 0) {
            $row_disclass = mysqli_fetch_assoc($getDisClass_qry);
            return $row_disclass['id_dis_class'];
        }
    }

    //функция для обновления данных в учебном плане в бд
    function compareData($dates, $perfomance){

        global $con;
        for($i = 0; $i<count($dates); $i++){
            if($dates[$i] != $perfomance[$i]['date_cur']){
                $qry = "UPDATE kod.curriculum SET date_cur = '$dates[$i]' where id_cur = ".$perfomance[$i]['id_cur'].";";
                mysqli_query($con, $qry);
            }
        }
    }
    echo "Данные переданы успешно";