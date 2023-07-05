<?php

    //query_helper.php
    //подключение файлов
    require '../../groupInfo.php';

    //инициализирование массивов
    $perfomance = array();
    $stud_list = array();
    $all_table = array();

    //функция для получения списка студентов по номеру группы
    function getStudList($group_num){
        global $stud_list;
        $stud_list = getStudentsList($group_num);
    }

    //функция для получения успеваемости студентов по номеру преподавателя, номеру дисциплины, номеру семестра и номеру группы
    function getStudPerform($teach_id, $disid, $semester, $group_num) {
        global $con, $perfomance, $stud_list;

        $perform_qry = "select distinct td.th_teach_id as 'id_teach', cur.id_cur, cur.date_cur, ct.name_type
                         from kod.curriculum cur
                         join kod.teach_discipline td on (cur.id_dis_class_cur = td.th_dis_class_id)
                         join kod.discipline_class_type dc ON (cur.id_dis_class_cur = dc.id_dis_class)
                         join kod.class_type ct ON (dc.dh_class_type_id = ct.id_class_type)
                         join kod.semester_discipline sd on (dc.dh_id_sem_dis = sd.sd_id_sem_dis)
                         join kod.discipline_major dm on (sd.sd_id_dis_maj = dm.id_dis_maj)
                         join kod.group g on (dm.dm_id_major = g.id_major)
                         where td.th_teach_id =$teach_id
                         and sd.sd_semester =$semester
                         and dm.dm_id_discipline = $disid
                         and cur.cur_group_num like '$group_num'
                         order by cur.date_cur";
        $perform_result = mysqli_query($con, $perform_qry);

        if(mysqli_num_rows($perform_result) > 0) {
            while ($row_perform = mysqli_fetch_assoc($perform_result)) {
                $perform = array(
                    "id_cur" => $row_perform['id_cur'],
                    "date_cur" => $row_perform['date_cur'],
                    "class_type" => $row_perform["name_type"],
                );

                $perform['stud_points'] = array();

                for ($i = 0; $i < count($stud_list); $i++) {
                    $stud_perform_qry = "SELECT * FROM kod.stud_perform where id_stud= ".$stud_list[$i]['id_stud']." and id_cur = ".$perform['id_cur'].";";
                    $stud_perform_result = mysqli_query($con, $stud_perform_qry);

                    if(mysqli_num_rows($stud_perform_result) > 0) {
                        $row_stud_perform = mysqli_fetch_assoc($stud_perform_result);
                        $perform_line = array(
                            "id_stud" => $row_stud_perform["id_stud"],
                            "points" => $row_stud_perform["points"],
                        );
                    } else {
                        $perform_line = array(
                            "id_stud" => $stud_list[$i]['id_stud'],
                            "points" => "",
                        );
                    }
                    $perform['stud_points'][] = $perform_line;
                }

                $perfomance[] = $perform;
            }
        }

        // ИТОГОВЫЕ БАЛЛЫ И ПРОПУСКИ
        for ($i = 0; $i < count($stud_list); $i++) {
            $stud_list[$i]['total_points'] = 0;
            $stud_list[$i]['passes'] = 0;

            $change_stud_list_qry = "SELECT * FROM kod.stud_attestation where id_stud = ".$stud_list[$i]['id_stud']." and id_discipline = $disid and sd_semester = $semester;";
            $change_stud_list_result = mysqli_query($con, $change_stud_list_qry);
            if(mysqli_num_rows($change_stud_list_result) > 0) {
                $row_change_stud_list = mysqli_fetch_assoc($change_stud_list_result);
                $stud_list[$i]['total_points'] = $row_change_stud_list['total_points'];
                $stud_list[$i]['passes'] = $row_change_stud_list['passes'];
            }
        }
    }

    //функция для получения номера текущего семестра по номеру группы
    function getSemester($group_num){

        global $con;
        $getSem_qry = mysqli_query($con, "SELECT addmission_year FROM kod.`group` where group_number like '$group_num';");
        if(mysqli_num_rows($getSem_qry) > 0) {
            $row_sem = mysqli_fetch_assoc($getSem_qry);

            $year = date('Y');
            $popr = 0;
            if(date('n')>8){
                $popr = 1;
            }
            return ($year-$row_sem['addmission_year'])*2+$popr;
        }
    }

    //функция для получения данных из бд по названию таблицы, названию столбца
    function getColumnData($table_name, $column, $index){
        global $con, $all_table;
        $helper = array();

        $get_all_table_qry = mysqli_query($con,"select * from kod.$table_name");
        if(mysqli_num_rows($get_all_table_qry) > 0) {
            while($row_all_table = mysqli_fetch_assoc($get_all_table_qry)){
                $helper[] = $row_all_table[$column];
            }
            $all_table[$index] = array (
                "col_name" => $column,
                "col_data" => $helper
            );
        }
    }
