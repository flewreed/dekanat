<?php

    //инициализирование массива
    $getMime = array();

    //функция для проверки допустимости загрузки картинки
    function can_upload($file){
        global $getMime;
        // если имя пустое, значит файл не выбран
        if($file['name'] == '')
            return 'Вы не выбрали файл.';

        /* если размер файла 0, значит его не пропустили настройки
        сервера из-за того, что он слишком большой */
        if($file['size'] == 0)
            return 'Файл слишком большой.';

        // разбиваем имя файла по точке и получаем массив
        $getMime = explode('.', $file['name']);
        // нас интересует последний элемент массива - расширение
        $mime = strtolower(end($getMime));
        $getMime[1] = ".".$getMime[1];
        // объявим массив допустимых расширений
        $types = array('jpg', 'png', 'gif', 'bmp', 'jpeg');

        // если расширение не входит в список допустимых - return
        if(!in_array($mime, $types))
            return 'Недопустимый тип файла.';
        return true;
    }

    //функция для загрузки фкартинки на сервер
    function make_upload($file){

        global $con, $getMime, $stud;
        // формируем уникальное имя картинки: случайное число и name
        $name = "students_photos/". $_SESSION['$user_id'] . $getMime[1];
        copy($file['tmp_name'], 'images/' . $name);

        mysqli_query($con,"update kod.stud set photo_stud = '$name' where s_id_user = ".$_SESSION['$user_id'].";");
    }
