<?php

    //uses_check.php
    // запрос на поиск пользователя в БД
    $result = mysqli_query($con,"select*from KOD.user where login like '".$_SESSION['login']."' and password like '".$_SESSION['password']."';");
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_access'] = $row['u_id_access'];
        $_SESSION['$user_id'] = $row['id_user'];
    } else {
        echo 	"<script>
                        alert('Введены неверные данные. Повторите попытку');
                 </script>";
        logout();
        exit;
    }