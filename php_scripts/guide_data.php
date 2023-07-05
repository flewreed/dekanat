<?php

    //guide_data.php
    //подключение файлов
    require 'config.php';

    //функция для получения данных преподавателей для отображения в справочнике
    function getTeachers(){
        global $con;

        $teachers = array();

        $teach_qry = "select*from kod.teach order by surname_teach, name_teach, patronymic_teach;";
        $teach_result = mysqli_query($con, $teach_qry);

        if(mysqli_num_rows($teach_result) > 0) {
            while ($row_teach = mysqli_fetch_assoc($teach_result)){
                $teach = array(
                    "teach_id" => $row_teach['id_teach'],
                    "surname_teach" => $row_teach['surname_teach'],
                    "name_teach" => $row_teach['name_teach'],
                    "patronymic_teach" => $row_teach['patronymic_teach'],
                    "current_phone_teach" => $row_teach['current_phone_teach'],
                    "internal_phone" => $row_teach['internal_phone'],
                    "current_mail_teach" => $row_teach['current_mail_teach'],

                    "depart_id" => $row_teach['id_depart'],
                );

                // определяем фото преподавателя по полу
                $path_image = "images/";
                if ($row_teach['photo_teach'] === 'default.png') {
                    if ($row_teach['gender_teach'] === 'м') {
                        $teach['photo_teach'] = $path_image.'default_m.png';
                    } else {
                        $teach['photo_teach'] = $path_image.'default_f.png';
                    }
                } else $teach['photo_teach'] = $path_image.$row_teach['photo_teach'];


                //ДОЛЖНОСТИ
                $post_qry = "SELECT t.id_teach, p.post_name FROM kod.teach t JOIN kod.teach_post tp ON (t.id_teach = tp.tp_id_teach)
                              JOIN kod.post p ON (tp.tp_id_post = p.id_post) where t.id_teach = '".$teach['teach_id']."' ORDER BY t.id_teach;";
                $post_result = mysqli_query($con, $post_qry);
                $post = array();
                if(mysqli_num_rows($post_result) > 0) {
                    while ($row_post = mysqli_fetch_assoc($post_result)){
                        $post[] = $row_post['post_name'];
                    }
                }
                $teach['post'] = $post;


                //СТЕПЕНЬ
                $degree_qry = "SELECT t.id_teach, d.name_degree FROM kod.teach t JOIN kod.teach_degree td ON (t.id_teach = td.td_id_teach)
                                JOIN kod.degree d ON (td.td_id_degree = d.id_degree) where t.id_teach = ".$teach['teach_id']." ORDER BY t.id_teach;";
                $degree_result = mysqli_query($con, $degree_qry);
                $degree = array();
                if(mysqli_num_rows($degree_result) > 0) {
                    while ($row_degree = mysqli_fetch_assoc($degree_result)){
                        $degree[] = $row_degree['name_degree'];
                    }
                }
                $teach['degree'] = $degree;


                $teachers[] = $teach;
            }
        }

        return $teachers;
    }

    //функция для получения данных студентов для отображения в справочнике
    function getStudents(){

        global $con;
        $students = array();

        $stud_qry = "select*from kod.stud_data;";
        $stud_result = mysqli_query($con, $stud_qry);
        if(mysqli_num_rows($stud_result) > 0) {
            while ($row_stud = mysqli_fetch_assoc($stud_result)) {
                $stud = array(
                    "id_user" => $row_stud['s_id_user'],
                    "id_stud" => $row_stud['id_stud'],
                    "surname_stud" => $row_stud['surname_stud'],
                    "name_stud" => $row_stud['name_stud'],
                    "patronymic_stud" => $row_stud['patronymic_stud'],
                    "phone" => $row_stud['current_phone_stud'],
                    "mail" => $row_stud['current_mail_stud'],
                    "country" => $row_stud['name_country'],
                    "code_major" => $row_stud['code_major'],
                    "name_major" => $row_stud['name_major'],
                    "group_number" => $row_stud['group_number'],
                    "gradebook_number" => $row_stud['gradebook_number'],
                    "education_stage" => $row_stud['name_education_stage'],
                    "education_form" => $row_stud['name_education_form'],
                    "group_year" => $row_stud['group_year'],
                    "education_basis" => $row_stud['name_education_basis'],
                    "addmission_type" => $row_stud['name_addmission_type'],
                );

                // определяем фото студента по полу
                $path_image = "images/";
                if ($row_stud['photo_stud'] === 'default.png') {
                    if ($row_stud['gender_stud'] === 'м') {
                        $stud['photo_stud'] = $path_image.'default_m.png';
                    } else {
                        $stud['photo_stud'] = $path_image.'default_f.png';
                    }
                } else $stud['photo_stud'] = $path_image.$row_stud['photo_stud'];

                $students[] = $stud;
            }
        }

        return $students;
    }

    //функция для получения всех данных студента
    function getStudentById($stud_id){
        global $con;
        $stud = array();

        $stud_qry = "select*from kod.stud where id_stud= $stud_id;";
        $stud_result = mysqli_query($con, $stud_qry);
        if(mysqli_num_rows($stud_result) > 0) {
            $row_stud = mysqli_fetch_assoc($stud_result);

            $stud = array(
                "id_user" => $row_stud['s_id_user'],
                "id_stud" => $row_stud['id_stud'],
                "surname_stud" => $row_stud['surname_stud'],
                "name_stud" => $row_stud['name_stud'],
                "patronymic_stud" => $row_stud['patronymic_stud'],
                "date_of_birth" => $row_stud['date_of_birth'],
                "gender" => $row_stud['gender_stud'],
                "phone" => $row_stud['current_phone_stud'],
                "mail" => $row_stud['current_mail_stud'],
                "id_ad_type" => $row_stud['id_addmission_type'],
                "id_ed_basis" => $row_stud['id_education_basis'],
                "ad_year" => $row_stud['addmission_year'],
                "gradebook_number" => $row_stud['gradebook_number'],
            );
        }
        $stud['parents'] = getParentsByStudId($stud_id);
        $stud['address'] = getAddressByStudId($stud_id);
        return $stud;
    }

    //функция для получения данных родителей по id студента
    function getParentsByStudId($stud_id) {
        global $con;
        $parents = array();

        $par_qry = "SELECT p.id_parent, p.surname_parent, p.name_parent, p.patronymic_parent, p.current_phone_parent, p.job
                    FROM kod.parent p
                    join kod.parent_stud ps on (p.id_parent = ps.id_ps_parent)
                    where ps.id_ps_stud = $stud_id;";
        $par_result = mysqli_query($con, $par_qry);
        if(mysqli_num_rows($par_result) > 0) {
            while ($row_par = mysqli_fetch_assoc($par_result)) {
                $par = array(
                    "id_parent" => $row_par['id_parent'],
                    "surname_parent" => $row_par['surname_parent'],
                    "name_parent" => $row_par['name_parent'],
                    "patronymic_parent" => $row_par['patronymic_parent'],
                    "phone_parent" => $row_par['current_phone_parent'],
                    "job" => $row_par['job'],
                );

                $parents[] = $par;
            }
        }
        return $parents;
    }

    //функция для получения адресов студента по id студента
    function getAddressByStudId($stud_id) {
        global $con;
        $address = array();

        $addr_qry = "SELECT a.id_address, a.id_type_addr, ct.name_country, a.oblast, a.city, a.street, a.house, a.flat
                    FROM kod.address a
                    join kod.country ct on (a.id_addr_country = ct.id_country)
                    join kod.stud_address sa on (a.id_address = sa.sa_id_addr)
                    where sa.sa_id_stud = $stud_id;";
        $addr_result = mysqli_query($con, $addr_qry);
        if(mysqli_num_rows($addr_result) > 0) {
            while ($row_addr = mysqli_fetch_assoc($addr_result)) {
                $addr = array(
                    "id_address" => $row_addr['id_address'],
                    "name_country" => $row_addr['name_country'],
                    "oblast" => $row_addr['oblast'],
                    "city" => $row_addr['city'],
                    "street" => $row_addr['street'],
                    "house" => $row_addr['house'],
                    "flat" => $row_addr['flat'],
                );

                $address[$row_addr['id_type_addr']] = $addr;
            }
        }
        return $address;
    }

    //функция для получения информацию о группе студента по id студента
    function getGroupInfoByStudId($stud_id) {
        global $con;
        $group = array();

        $group_qry = "SELECT g.id_group, g.addmission_year, g.id_education_form, g.id_major, dm.dpm_id_depart, ds.ds_id_subdivision
                        FROM kod.group g
                        join kod.group_stud gs on (g.id_group = gs.gs_group_id)
                        join kod.depart_major dm on (g.id_major = dm.dpm_id_major)
                        join kod.depart_subdivision ds on (dm.dpm_id_depart = ds.ds_id_depart)
                        where gs.gs_stud_id = $stud_id;";
        $group_result = mysqli_query($con, $group_qry);
        if(mysqli_num_rows($group_result) > 0) {
            $row_group = mysqli_fetch_assoc($group_result);
            $group = array(
                "id_group" => $row_group['id_group'],
                "addmission_year" => $row_group['addmission_year'],
                "id_ed_form" => $row_group['id_education_form'],
                "id_major" => $row_group['id_major'],
                "id_depart" => $row_group['dpm_id_depart'],
                "id_subdivision" => $row_group['ds_id_subdivision'],
            );

            $year = date('Y');
            $popr=0;
            if(date('n')>8){
                $popr = 1;
            }
            $group['course'] = $year - $group['addmission_year'] + $popr;
        }
        return $group;
    }

    //функция для получения названия кафедр
    function getDepartments() {

        global $con;
        $departments = array();

        $depart_qry = "select*from kod.depart order by name_depart;";
        $depart_result = mysqli_query($con, $depart_qry);

        if(mysqli_num_rows($depart_result) > 0) {
            while ($row_depart = mysqli_fetch_assoc($depart_result)){
                $departments[] = $row_depart['name_depart'];
            }
        }

        return $departments;
    }

    //функция для получения списка дисциплин
    function getDisciplines(){

        global $con;
        $disciplines = array();

        $dis_qry = "select*from kod.discipline order by name_discipline;";
        $dis_result = mysqli_query($con, $dis_qry);

        if(mysqli_num_rows($dis_result) > 0) {
            while ($row_dis = mysqli_fetch_assoc($dis_result)){
                $disciplines[] = $row_dis['name_discipline'];
            }
        }

        return $disciplines;
    }

    //функция для получения списка годов поступления
    function getAddmissionYears() {
        $years = array();
        $cur_year = date('Y');
        for($i = 0; $i < 5; $i++) {
            $years[] = $cur_year - $i;
        }
        return $years;
    }

    //функция для получения списка типов поступления
    function getAddmissionType() {
        global $con;
        $ad_type = array();

        $type_qry = "SELECT * FROM kod.addmission_type order by name_addmission_type;";
        $type_result = mysqli_query($con, $type_qry);

        if(mysqli_num_rows($type_result) > 0) {
            while ($row_type = mysqli_fetch_assoc($type_result)){
                $type = array(
                    "id_addmission_type" => $row_type['id_addmission_type'],
                    "name_addmission_type" => $row_type['name_addmission_type'],
                );
                $ad_type[] = $type;
            }
        }

        return $ad_type;
    }

    //функция для получения списка типов основы обучения
    function getEducationBasis() {
        global $con;
        $ed_basis = array();

        $basis_qry = "SELECT * FROM kod.education_basis order by name_education_basis;";
        $basis_result = mysqli_query($con, $basis_qry);

        if(mysqli_num_rows($basis_result) > 0) {
            while ($row_basis = mysqli_fetch_assoc($basis_result)){
                $basis = array(
                    "id_education_basis" => $row_basis['id_education_basis'],
                    "name_education_basis" => $row_basis['name_education_basis'],
                );
                $ed_basis[] = $basis;
            }
        }

        return $ed_basis;
    }

    //функция для получения списка форм образования
    function getEducationForm() {
        global $con;
        $ed_form = array();

        $form_qry = "SELECT * FROM kod.education_form order by id_education_form;";
        $form_result = mysqli_query($con, $form_qry);

        if(mysqli_num_rows($form_result) > 0) {
            while ($row_form = mysqli_fetch_assoc($form_result)){
                $form = array(
                    "id_education_form" => $row_form['id_education_form'],
                    "name_education_form" => $row_form['name_education_form'],
                );
                $ed_form[] = $form;
            }
        }

        return $ed_form;
    }

    //функция для получения списка факультетов
    function getSubdivisions(){
        global $con;
        $subdivisions = array();

        $subdivisions_qry = "SELECT * FROM kod.subdivision order by name_subdivision;";
        $subdivisions_result = mysqli_query($con, $subdivisions_qry);

        if(mysqli_num_rows($subdivisions_result) > 0) {
            while ($row_subdivisions = mysqli_fetch_assoc($subdivisions_result)){
                $subd = array(
                    "id_subdivision" => $row_subdivisions['id_subdivision'],
                    "name_subdivision" => $row_subdivisions['name_subdivision'],
                );
                $subdivisions[] = $subd;
            }
        }

        return $subdivisions;
    }

    //функция для получения списка кафедр по id факультета
    function getDepartmentsBySubId($sub_id) {
        global $con;
        $departments = array();

        $dep_qry = "SELECT d.id_depart, d.name_depart
                    FROM kod.depart d
                    join kod.depart_subdivision ds on (d.id_depart = ds.ds_id_depart)
                    where ds.ds_id_subdivision = $sub_id;";
        $dep_result = mysqli_query($con, $dep_qry);

        if(mysqli_num_rows($dep_result) > 0) {
            while ($row_dep = mysqli_fetch_assoc($dep_result)){
                $dep = array(
                    "id_depart" => $row_dep['id_depart'],
                    "name_depart" => $row_dep['name_depart'],
                );
                $departments[] = $dep;
            }
        }

        return $departments;
    }

    //функция для получения списка направлений по id кафедры
    function getMajorByDepId($dep_id) {
        global $con;
        $majors = array();

        $maj_qry = "SELECT m.id_major, m.code_major, m.name_major, es.name_stage
                    FROM kod.major m
                    join kod.depart_major dm on (m.id_major = dm.dpm_id_major)
                    join kod.education_stage es on (m.m_id_education_stage = es.id_education_stage)
                    where dm.dpm_id_depart = $dep_id
                    order by m.m_id_education_stage, m.name_major;";
        $maj_result = mysqli_query($con, $maj_qry);

        if(mysqli_num_rows($maj_result) > 0) {
            while ($row_maj = mysqli_fetch_assoc($maj_result)){
                $maj = array(
                    "id_major" => $row_maj['id_major'],
                    "code_major" => $row_maj['code_major'],
                    "name_major" => $row_maj['name_major'],
                    "name_stage" => $row_maj['name_stage'],
                );
                $majors[] = $maj;
            }
        }

        return $majors;
    }

    //функция для получения списка групп по id направления, форме образования и курсу
    function getGroups($major_id, $ed_form, $course) {
        global $con;
        $groups = array();

        $year = date('Y');
        $popr=0;
        if(date('n')>8){
            $popr = 1;
        }

        $adm_year = $year - $course + $popr;

        $groups_qry = "SELECT id_group, group_number
                        FROM kod.group
                        where id_education_form = $ed_form
                        and id_major = $major_id
                        and addmission_year = $adm_year;";
        $groups_result = mysqli_query($con, $groups_qry);

        if(mysqli_num_rows($groups_result) > 0) {
            while ($row_groups = mysqli_fetch_assoc($groups_result)){
                $gr = array(
                    "id_group" => $row_groups['id_group'],
                    "group_number" => $row_groups['group_number'],
                );
                $groups[] = $gr;
            }
        }

        return $groups;
    }
?>