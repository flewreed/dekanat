<?php

    //query_admin_show_table1.php
    //подключение файлов
    require '../../config.php';
    require '../query_helper.php';

    //инициализация глобальной переменной
    global $all_table;

    //получение данных из форм
    $table_name = $_POST['table_name'];
    $column_name = $_POST['column_name'];

    //вызов функции, для получения данных из выбранного столбца
    getColumnData($table_name, $column_name, 0);

    //формирование выбранного столбца
    for($j=0; $j<count($all_table[0]['col_data']); $j++){
        echo  "<tr><td><input type=\"text\" class=\"table1cell\" value='\"".$all_table[0]['col_data'][$j]."\"'></td></tr>";
    }
