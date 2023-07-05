<?php

    //query_admin_select_table_and_column.php
    //подключение файлов
    require '../../config.php';
    require '../query_helper.php';

    //инициализирование строки-заполнения селекта
    $select_column="";

    //инициализирование массива
    $columns = array();

    //получение данных из форм
    $table_name = $_POST['table_name'];

    //вызов функции, для получения всех названий столбцов выбранной таблицы
    getColumns($table_name);

    //формирование селекта по выбранной таблице
    $select_column .= "<option value=\"choose\">--Выберите столбец--</option>";
    for ($i = 0; $i < count($columns); $i++) {
        global $all_table;
        getColumnData($table_name, $columns[$i], $i);
        $select_column .= "<option value=\"$columns[$i]\">" . $columns[$i] . "</option>";
    }

    //функции, для получения всех названий столбцов выбранной таблицы
    function getColumns($table_name){
        global $con, $columns;

        $get_column_qry = mysqli_query($con, "show columns FROM kod.$table_name");
        if(mysqli_num_rows($get_column_qry) > 0) {
            while($row_column = mysqli_fetch_assoc($get_column_qry)){
               $columns[] = $row_column['Field'];
            }
        }
    }

    //формирование таблицы по выбранному названию
    $table2 = "<tr>";

    for($i=0; $i<count($columns); $i++){
        $table2 .= "<th class=\"table2header\" id=\"colname\">$columns[$i]</th>";
    }
    $table2 .= "</tr>";

    for($i=0; $i<count($all_table[0]['col_data']); $i++){
        $table2 .= "<tr>";
        for($j=0; $j<count($columns); $j++){
            $table2 .= "<td><input class = \"table2cell\" type=\"text\" id=\"любое\" value='".$all_table[$j]['col_data'][$i]."'></td>";
        }
        $table2 .= "</tr>";
    }

    //формирование json-файла для передачи данных в формы
    echo json_encode(array( 'select_column' => $select_column, 'table2' => $table2));

