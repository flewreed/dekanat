<?php

    //deanery_data.php
    //подключение файлов
    require 'config.php';

    //функция для получения данных сотрудника деканата
    function getDeaneryUser(){
        global $con;

        $deanery_qry = "";
        $surname = "";
        $name = "";
        $patronymic = "";

        switch($_SESSION['user_access']){
            // деканат
            case 4:
                $deanery_qry = "SELECT * FROM kod.dekanat_user where id_user_dekanat = ".$_SESSION['$user_id'].";";
                $surname = "surname_dekanat";
                $name = "name_dekanat";
                $patronymic = "patronymic_dekanat";
                break;

            // преподаватель/деканат
            case 6:
                $deanery_qry = "select*from kod.teach where id_user_teach = ".$_SESSION['$user_id'].";";
                $surname = "surname_teach";
                $name = "name_teach";
                $patronymic = "patronymic_teach";
                break;
        }
        $deanery_result = mysqli_query($con, $deanery_qry);

        if(mysqli_num_rows($deanery_result) > 0) {

            $row_deanery = mysqli_fetch_assoc($deanery_result);
            $deaneryUser = array(
                "surname" => $row_deanery[$surname],
                "name" => $row_deanery[$name],
                "patronymic" => $row_deanery[$patronymic],
            );

            return $deaneryUser;
        } else {

            // !!! Пользователь имеет либо не те права в таблице kod.access, либо отсутствует таблице kod.stud !!!
            // !!! Указать почту для обращения при ошибке !!!

            echo 	"<script>
                        alert('Недостаточно прав доступа. Обратитесь к администратору сайта: mail.ru');
                 </script>";
            logout();
            exit;
        }
    }

?>