<?php

    //groupInfo.php
    //инициализирование массива
    $groupStudents = array();

    //функция для получения списка дисциплин и их тип аттестации по номеру семестра и номеру группы
    function getDisciplinies ($semester, $group_number) {
        $disciplinies = array();
        // запрос поиска студента по id пользователя
        $con = mysqli_connect("localhost", "root", "root", "KOD");
        $dis_qry = "select*from kod.dis_data where group_number like '$group_number' and sd_semester =$semester;";
        $dis_result = mysqli_query($con, $dis_qry);
        $con->close();
        if(mysqli_num_rows($dis_result) > 0) {
            while ($row_dis = mysqli_fetch_assoc($dis_result)){
                $dis = array(
                    "id_discipline" => $row_dis['id_discipline'],
                    "name_discipline" => $row_dis['name_discipline'],
                    "name_attestation" => $row_dis['name_attestation'],
                    );
                $disciplinies[]=$dis;
            }
        }
        return $disciplinies;
    }

    //функция для получения списка студентов по номеру группы
    function getStudentsList ($group_number) {
        global $groupStudents;

        $group_qry = "select*from kod.stud_data where group_number='".$group_number."';";
        $group_result = mysqli_query(mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE), $group_qry);
        if(mysqli_num_rows($group_result) > 0) {
            while ($row_group = mysqli_fetch_assoc($group_result)){
                $student = array(
                    "user_id" => $row_group['s_id_user'],
                    "id_stud" => $row_group['id_stud'],
                    "surname_stud" => $row_group['surname_stud'],
                    "name_stud" => $row_group['name_stud'],
                    "patronymic_stud" => $row_group['patronymic_stud'],
                );
                $groupStudents[] = $student;
            }
        }
        return $groupStudents;
    }

    //инициализирование массива
    $groupStudentsRating = array();

    //функция для получения рейтинга группы по номеру группы и номеру семестра
    function getSemesterGroupRating($group_number, $semester){
        $con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);

        getStudentsList($group_number);
        global $groupStudents, $groupStudentsRating;

        $groupStudentsRating = $groupStudents;
        $save_sort = $groupStudents;

        for ($i = 0; $i < count($groupStudentsRating); $i++) {
            $groupStudentsRating[$i]['sum_points'] = 0;
            $groupStudentsRating[$i]['rating'] = 1;

            $stud_qry = "select sum(total_points) as 'sum_points' from kod.stud_attestation
                            where s_id_user = ".$groupStudentsRating[$i]['user_id']." and sd_semester = ".$semester." group by s_id_user, sd_semester;";
            $stud_result = mysqli_query($con, $stud_qry);

            if(mysqli_num_rows($stud_result) > 0) {
                while ($row_group = mysqli_fetch_assoc($stud_result)) {
                    $groupStudentsRating[$i]['sum_points'] = $row_group['sum_points'];
                }
            }
        }

        // сортировка по убыванию
        function cmp_function($a, $b){
            return ($a['sum_points'] < $b['sum_points']);
        }
        uasort($groupStudentsRating, 'cmp_function');
        $groupStudentsRating = array_values($groupStudentsRating);
        // определение рейтинга
        for ($i = 1; $i < count($groupStudentsRating); $i++) {
            if ($groupStudentsRating[$i]['sum_points'] !== $groupStudentsRating[$i-1]['sum_points']) {
                $groupStudentsRating[$i]['rating'] = $groupStudentsRating[$i-1]['rating'] + 1;
            } else {
                $groupStudentsRating[$i]['rating'] = $groupStudentsRating[$i-1]['rating'];
            }
        }

        for ($i = 0; $i < count($groupStudentsRating); $i++) {
            for ($k = 0; $k < count($save_sort); $k++) {
                if ($save_sort[$k]['user_id'] === $groupStudentsRating[$i]['user_id']) {
                    $save_sort[$k]['sum_points'] = $groupStudentsRating[$i]['sum_points'];
                    $save_sort[$k]['rating'] = $groupStudentsRating[$i]['rating'];
                }
            }
        }

        $groupStudentsRating = $save_sort;
        return $groupStudentsRating;
    }
 ?>