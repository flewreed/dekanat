<?php
	require 'php_scripts/session.php';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style/style.css">
    <link rel="stylesheet" type="text/css" href="style/media.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400&display=swap" rel="stylesheet">
	<title>Администратору</title>
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){

            $('#admintable').on('change', sel_data);
            $('#opentablebtn').click(sel_data_btn);
            function sel_data (){ // задаем функцию при нажатиии на элемент <button>
                var table_name = $('#admintable').val();

                if (table_name !== 'choose'){
                    $.ajax({
                        method: "POST", // метод HTTP, используемый для запроса
                        url: "php_scripts/query_postdata/admin/query_admin_select_table_and_column.php", // строка, содержащая URL адрес, на который отправляется запрос
                        data: { // данные, которые будут отправлены на сервер
                            table_name: table_name,
                        },
                        success: [function (data) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)

                            data = JSON.parse(data);
                            $('#admincolumn').html(data.select_column);
                            $('#selectedtable').html(data.table2);
                        }]
                    })
                }
                else{
                    $('#selectedcol').empty();
                    $("#admincolumn").html(" <option value=\"choose\">--Выберите столбец--</option>");
                    $('#selectedtable').empty();
                }
            }
            function sel_data_btn (){ // задаем функцию при нажатиии на элемент <button>
                var table_name = $('#admintable').val();
                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/admin/query_admin_select_table_and_column.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        table_name: table_name,
                    },
                    success: [function (data) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)

                        data = JSON.parse(data);
                        $('#selectedtable').html(data.table2);
                    }]
                })
            }

            $('#admincolumn').on('change', showColumn);
            $('#openselectedbtn').click(showColumn);

            function showColumn(){ // задаем функцию при нажатиии на элемент <button>
                var table_name = $('#admintable').val();
                var column_name = $('#admincolumn').val();

                if (table_name !== 'choose' && column_name !== 'choose'){
                    $.ajax({
                        method: "POST", // метод HTTP, используемый для запроса
                        url: "php_scripts/query_postdata/admin/query_admin_show_table1.php", // строка, содержащая URL адрес, на который отправляется запрос
                        data: { // данные, которые будут отправлены на сервер
                            table_name: table_name,
                            column_name: column_name
                        },
                        success: [function (data) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                            $('#selectedcol').html(data);
                        }]
                    })
                }
                else{
                    $('#selectedcol').empty();
                }
            }

            $('#acceptcols').click(function(){ // задаем функцию при нажатиии на элемент <button>
                var table_name = $('#admintable').val();
                var column_name = $('#admincolumn').val();
                var cells = [];
                $('.table1cell').each(function () {
                    cells.push($(this).val());
                });
                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/admin/query_admin_edit_table1.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        table_name: table_name,
                        column_name: column_name,
                        cells: cells
                    },
                    success: [function (msg) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                        alert(msg);
                    }]
                })
            });

            $('#accepttable').click(function(){ // задаем функцию при нажатиии на элемент <button>
                var table_name = $('#admintable').val();
                var cells = [];
                $('.table2cell').each(function () {
                    cells.push($(this).val());
                });
                var columns = [];
                $('.table2header').each(function () {
                    columns.push($(this).text());
                });
                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/admin/query_admin_edit_table2.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        table_name: table_name,
                        cells: cells,
                        columns: columns
                    },
                    success: [function (msg) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                        alert(msg);
                    }]
                })
            });

            $('#getrequest').click(function(){ // задаем функцию при нажатиии на элемент <button>
                var qry = $('.table3query').val();
                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/admin/query_admin_table3.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        qry: qry
                    },
                    success: [function (msg) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                        alert(msg);
                    }]
                })
            });
        });
    </script>
</head>
<body>
<div id="header">
	<img src="images/logo.png" alt="Логотип" width="130px"/>
	<h1><b>Балльно-рейтинговая система</b></h1>
	<h3>Санкт-Петербургский государственный технологический институт</h3>

	<a href="?logout">
		<Button id="exitbutton"><h3>Выйти</h3></Button>
	</a>
</div>

<div id="container">
    <div id="adminpage">
        <h4>Таблица:</h4>
        <div>
            <select class="admintables" id="admintable">
                <option value="choose">--Выберите таблицу--</option>
                <?php
                    require 'php_scripts/config.php';
//                    global $con;
                    $get_tables_qry = mysqli_query($con, "show tables from kod");
                    if(mysqli_num_rows($get_tables_qry) > 0) {
                        while($row_tables = mysqli_fetch_assoc($get_tables_qry)){
                            echo "<option value='".$row_tables['Tables_in_kod']."'>".$row_tables['Tables_in_kod']."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <h4>Столбец:</h4>
        <div>
            <select class="admintablescols" id="admincolumn">
                <option value="choose">--Выберите столбец--</option>
            </select>
        </div>

        <button id="openselectedbtn" class="tabl" onclick="opendata(event, 'openselected')"><h3>Вывести данные</h3></button>
        <button id="opentablebtn" class="tabl" onclick="opendata(event, 'opentable')"><h3>Вывести таблицу целиком</h3></button>
        <button id="createrequestbtn" class="tabl" onclick="opendata(event, 'createrequest')"><h3>Создать запрос</h3></button>

        <div id="openselected" class="tabc" style="display: none">
            <div class="scroll">
                <table id="selectedcol"></table>
            </div>
            <button id="acceptcols">Применить</button>
        </div>
        <div id="opentable" class="tabc" style="display: none">
            <div class="scroll">
                <table id="selectedtable"></table>
            </div>
            <button id="accepttable">Применить</button>
        </div>
        <div id="createrequest" class="tabc" style="display: none">
            <h4>Введите запрос:</h4>
            <br>
            <br>
            <textarea class ="table3query"></textarea>
            <br>
            <button id="getrequest">Создать</button>
        </div>
    </div>
</div>

<div id="footer">
	<p> &copy; 2022, Факультет информационных технологий и управления,<br>
		Санкт-Петербургский государственный технологический институт<br>
		(Технический университет)</p>
</div>
<script type="text/javascript" src="js_scripts/buttons.js"></script>
</body>
</html>