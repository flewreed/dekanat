<?php

    //query_admin_table3.php
    //подключение файлов
    require '../../config.php';

    //получение данных из форм
    $qry = $_POST['qry'];

    //проверка введеного запроса
    if(mb_strpos(strtolower($qry),"select") === 0){
        echo "Некорректный запрос";
        exit(0);
    }
    if(mysqli_query($con, $qry)){
        echo "Запрос выполнен успешно";
    }
    else{
        printf("Сообщение ошибки: %s\n", mysqli_error($con));
    }
