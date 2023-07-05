<?php

    //query_elderstudent_add_miss.php
    //подключение файлов
    require '../../elderstudent_data.php';
	require '../../groupInfo.php';
    require '../../session.php';

    //инициализирование массива
    $checks = array();

    //вызов функции для получения данных группы
    getGroupInfo();

    //инициализирование переменной
    $group_num = $groupInfo['group_number'];
    $maj=$groupInfo['name_major'];
    $semester = $groupInfo['semester'];

    //получение списка студентов по номеру группы
	getStudentsList($group_num);

    //получение данных из форм
    $checks = $_POST['checks'];
    $date = $_POST['date'];
    $typeoflesson = $_POST['typeoflesson'];
    $disciplinename = $_POST['disciplinename'];

    //вызов функции для получения номера id_disclass из бд
    $disclass = getDisClass($disciplinename, $maj, $typeoflesson, $semester);

    //вызов функции для определения номера id_cur из бд
    $curid = getCurId($date, $disclass, $group_num);

    //формирование строки-запроса для изменения данных в бд
    for( $i=0; $i<count($checks); $i++){
        global $con;
        $stud_num = $groupStudents[$checks[$i]]['id_stud'];
        checkStudPoint($stud_num, $curid);
        $add_miss = "UPDATE kod.stud_performance SET points = -1 where sp_id_stud = $stud_num and sp_id_cur = $curid;";
        mysqli_query($con,$add_miss);
    }

    //функция для получения id_disclass по названию дисциплины. направлению, типу занятия и номера семестра
    function getDisClass($dis, $maj, $typeoflesson, $semester){
        global $con;
        $getDisClass_qry = mysqli_query($con, "SELECT id_dis_class FROM kod.discipline_class_type where dh_id_sem_dis in (SELECT sd_id_sem_dis FROM kod.semester_discipline  where sd_id_dis_maj in (SELECT id_dis_maj FROM kod.discipline_major where dm_id_discipline in (SELECT id_discipline FROM kod.discipline where name_discipline like '$dis') and dm_id_major in (SELECT id_major FROM kod.major where name_major like '$maj')) and sd_semester=$semester) and dh_class_type_id in (SELECT id_class_type FROM kod.class_type where name_type like '$typeoflesson');");
        if(mysqli_num_rows($getDisClass_qry) > 0) {
            $row_disclass = mysqli_fetch_assoc($getDisClass_qry);
            return $row_disclass['id_dis_class'];
        }
    }

    //функция для получения id_cur из бд по дате, disclass и номеру группы
    function getCurId($date, $disclass, $group_num){
        global $con;

        $getCurId_qry = mysqli_query($con, "SELECT id_cur FROM kod.curriculum where date_cur='$date' and id_dis_class_cur = $disclass and cur_group_num like '$group_num';");
        if(mysqli_num_rows($getCurId_qry) > 0) {
            $row_curid = mysqli_fetch_assoc($getCurId_qry);
            return $row_curid['id_cur'];
        }else{
            echo "Такого занятия не существует";
            exit(0);
        }
    }

    //функция для проверки баллов студента (нельзя поставить пропуск, если у студента за это занятие уже стоят баллы)
    function checkStudPoint($stud_id, $curid){
        global $con;

        $getStudPoint = mysqli_query($con, "SELECT points FROM kod.stud_performance where sp_id_stud = $stud_id and sp_id_cur = $curid");
        if(mysqli_num_rows($getStudPoint) > 0) {
            $row_getpoints = mysqli_fetch_assoc($getStudPoint);
            if($row_getpoints['points'] > 0 ){
                echo "За выбранное занятие у студента стоят баллы";
                exit(0);
            }
        }
    }
