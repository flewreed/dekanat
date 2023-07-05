<?php

    //query_admin_edit_table2
    //подключение файлов
    require '../../config.php';
    require '../query_helper.php';

    //инициализирование массивов
    $cells = array();
    $columns = array();

    //получение данных из форм
    $table_name = $_POST['table_name'];
    $cells = $_POST['cells'];
    $columns = $_POST['columns'];

    //получение данных для столцов всей выбранной таблицы
    for($i=0; $i<count($columns); $i++){
        global $all_table;
        //вызов функции, для получения данных из выбранного столбца
        getColumnData($table_name, $columns[$i], $i);
    }

    //формирование стоки-запроса, для обновления данных таблицы в бд
    for($i=0; $i<count($all_table[0]['col_data']); $i++){
        for($j=0; $j<count($columns); $j++){
            global $con, $table_name;
            if($all_table[$j]['col_data'][$i] != $cells[$i*count($columns)+$j]){
                $edit_table2_qry = "UPDATE kod.$table_name
                            INNER JOIN (SELECT $columns[$j] from kod.$table_name limit $i,1) as tb using ($columns[$j])
                            SET $columns[$j] = '".$cells[$i*count($columns)+$j]."'";
                if(!mysqli_query($con, $edit_table2_qry)){
                    printf("Сообщение ошибки: %s\n", mysqli_error($con));
                    exit(0);
                }
            }
        }
    }
    echo "Данные успешно переданы";
