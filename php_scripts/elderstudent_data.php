<?php

    //elderstudent_data.php
    //подключение файлов
    require 'config.php';

    //инициализирование массива
    $groupInfo = array();

    //функция для получения информации о группе для старосты
    function getGroupInfo(){
        // запрос поиска студента по id пользователя

        global $groupInfo, $con;

        $elderstud_qry = "select*from kod.stud_data where s_id_user=".$_SESSION['$user_id'].";";
        $elderstud_result = mysqli_query($con, $elderstud_qry);
        if(mysqli_num_rows($elderstud_result) > 0) {

            $row_stud = mysqli_fetch_assoc($elderstud_result);
            $groupInfo = array(
                "code_major" => $row_stud['code_major'],
                "name_major" => $row_stud['name_major'],
                "group_number" => $row_stud['group_number'],
            );

            $month = date('n');

            $semester = (date('Y') - $row_stud['group_year'])*2;
            if ($month>8){
                $semester++;
            }
            $groupInfo['semester'] = $semester;
        } else {

            // !!! Пользователь имеет либо не те права в таблице kod.access, либо отсутствует таблице kod.stud !!!
            // !!! Указать почту для обращения при ошибке !!!

            echo 	"<script>
                        alert('Вы не являетесь старостой. Обратитесь к администратору сайта: mail.ru');
                 </script>";
            logout();
            exit;
        }
    }
?>