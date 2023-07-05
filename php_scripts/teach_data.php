<?php

    //teach_data.php
    //подключение файлов
    require 'config.php';

    //функция для получения информации о преподавателе
    function getTeacherData(){

        global $con;
        $teacher = array();

        $teach_qry = "select*from kod.teach where id_user_teach = ".$_SESSION['$user_id'].";";
        $teach_result = mysqli_query($con, $teach_qry);

        if(mysqli_num_rows($teach_result) > 0) {

            $row_teach = mysqli_fetch_assoc($teach_result);
            $teacher = array(
                "id_teach" => $row_teach['id_teach'],
                "surname_teach" => $row_teach['surname_teach'],
                "name_teach" => $row_teach['name_teach'],
                "patronymic_teach" => $row_teach['patronymic_teach'],
            );

            //КАФЕДРА
            $dep_qry = "select*from kod.depart where id_depart = ".$row_teach['id_depart'].";";
            $dep_result = mysqli_query($con, $dep_qry);
            if(mysqli_num_rows($dep_result) > 0) {
                $row_dep = mysqli_fetch_assoc($dep_result);
                $teacher['name_depart'] = $row_dep['name_depart'];
            }

            return $teacher;
        } else {

            // !!! Пользователь имеет либо не те права в таблице kod.access, либо отсутствует таблице kod.stud !!!
            // !!! Указать почту для обращения при ошибке !!!

            echo 	"<script>
                        alert('Данного преподавателя не существует. Обратитесь к администратору сайта: mail.ru');
                 </script>";
            logout();
            exit;
        }
    }

    //инициализирование массива
    $teacher_dis = array();

    //фукнция для получения списка дисциплин преподавателя
    function getTeachPlan ($teach_id){

        global $teacher_dis,$con;

        $year = date('Y');
        $popr=0;
        if(date('n')>8){
            $popr = 1;
        }
        $quer = "SELECT name_discipline,id_discipline FROM kod.stud_plan WHERE id_teach = $teach_id AND semester = ($year-addmission_year)*2+$popr GROUP BY name_discipline;";
        $teach_dis = mysqli_query($con, $quer);
        if(mysqli_num_rows($teach_dis) > 0) {
            while($row_teach_dis = mysqli_fetch_assoc($teach_dis)){
                $td = array(
                    "dis_name" => $row_teach_dis['name_discipline'],
                    "dis_id" => $row_teach_dis['id_discipline']
                );
                $teacher_dis[]=$td;
            }
        }
    }
?>