<?php

    //query_admin_edit_table1
    //поключение файлов
    require '../../config.php';
    require '../query_helper.php';

    //объявление глобальных переменных
    global $all_table;
    $cells = array();

    //получение данных из форм
    $table_name = $_POST['table_name'];
    $column_name = $_POST['column_name'];
    $cells = $_POST['cells'];

    //инициализирование строки-запроса
    $edit_cell = "";

    //вызов функции, для получения данных из выбранного столбца
    getColumnData($table_name, $column_name, 0);

    //формирование стоки-запроса, для обновления данных таблицы в бд
    for($i=0; $i<count($cells); $i++){
        global $con;
        if($cells[$i] != $all_table[0]['col_data'][$i]){
            $edit_cell = "UPDATE kod.$table_name
                            INNER JOIN (SELECT $column_name from kod.$table_name limit $i,1) as tb using ($column_name)
                            SET $column_name = $cells[$i]";
            if(!mysqli_query($con, $edit_cell)){
                printf("Сообщение ошибки: %s\n", mysqli_error($con));
                exit(0);
            }
        }
    }
    echo "Данные успешно переданы";


