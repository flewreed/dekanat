<?php

    //query_teacher_add_points.php
    //подключение файлов
    require '../../config.php';
    require '../query_helper.php';

    //инициализирование массива
    $studs = array();

    //получения данных из форм
    $studs = $_POST['studs'];
    $disid = $_POST['disid'];
    $group_num = $_POST['group_num'];
    $lessontypeid = $_POST['lessontypeid'];
    $date = $_POST['date'];

    //вызов функции для получения списка студентов по номеру группы
    getStudList($group_num);

    //инициализирование глобальной переменной
    global $stud_list;

    //инициализирование переменных
    $semester = getSemester($group_num);

    //вызов функции для получения id_disclass из бд
    $disclass = getDisClass($disid, $group_num, $lessontypeid, $semester);

    //формирование строк-запросов для добавления данных в бд
    $add_in_cur = "INSERT INTO kod.curriculum (date_cur, id_dis_class_cur, cur_group_num) VALUES ";
    $add_miss="INSERT INTO kod.stud_performance (sp_id_stud, sp_id_cur, points) VALUES ";

    //вызов функции для получения id_cur из бд
    $curid = getCurId($date, $disclass, $group_num);

    //проверка на создание новой записи, если ее не существует
    if(is_null($curid)){
        global $con;
        $add_in_cur .= "('$date', $disclass, '$group_num');";
        mysqli_query($con, $add_in_cur);
        $curid = getCurId($date, $disclass, $group_num);
    }

    //формирование строки-запроса для добавления данных об успеваемости в бд
    for( $i=0; $i<count($studs); $i++){
        $stud_num = $stud_list[$i]['id_stud'];
        $add_miss .= "('$stud_num','$curid', $studs[$i])";
        if($i != count($studs)-1){
            $add_miss .= ",";
        }
    }

    //вызов функции для отправления данных на сервер
    setMiss($add_miss);

    //функция для отправления данных на сервер
    function setMiss($add_miss){
        global $con;
        mysqli_query($con,$add_miss);
    }

    //функция для получения id_disclass из бд по номеру дисциплины, номеру группы, типу занятия и семестру
    function getDisClass($disid, $group_num, $typeoflesson, $semester){
        global $con;

        $getDisClass_qry = mysqli_query($con,"SELECT id_dis_class FROM kod.discipline_class_type where dh_id_sem_dis in (SELECT sd_id_sem_dis FROM kod.semester_discipline where sd_id_dis_maj in (SELECT id_dis_maj FROM kod.discipline_major where dm_id_discipline = $disid and dm_id_major in (SELECT id_major FROM kod.`group` where group_number like '$group_num')) and sd_semester=$semester) and dh_class_type_id in (SELECT id_class_type FROM kod.class_type where name_type like '$typeoflesson');");
        if(mysqli_num_rows($getDisClass_qry) > 0) {
            $row_disclass = mysqli_fetch_assoc($getDisClass_qry);
            return $row_disclass['id_dis_class'];
        }
    }

    //функция для получения id_cur по дате, номеру disclass и номеру группы из бд
    function getCurId($date, $disclass, $group_num){
        global $con;

        $getCurId_qry = mysqli_query($con, "SELECT id_cur FROM kod.curriculum where date_cur='$date' and id_dis_class_cur = $disclass and cur_group_num like '$group_num';");
        if(mysqli_num_rows($getCurId_qry) > 0) {
            $row_curid = mysqli_fetch_assoc($getCurId_qry);
            return $row_curid['id_cur'];
        }else{
            return null;
        }
    }