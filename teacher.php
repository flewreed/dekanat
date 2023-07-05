<?php
	require 'php_scripts/session.php';
	require 'php_scripts/teach_data.php';

	$teacher = getTeacherData();
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
	<title>Преподавателю</title>

    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){

            $('#disid').on('change', function(){ // задаем функцию при нажатиии на элемент <button>
                var disid = $('#disid').val();

                if(disid !== "choose"){
                    $.ajax({
                        method: "POST", // метод HTTP, используемый для запроса
                        url: "php_scripts/query_postdata/teacher/query_teacher_disciplinies.php", // строка, содержащая URL адрес, на который отправляется запрос
                        data: { // данные, которые будут отправлены на сервер
                            disid: disid,
                        },
                        success: [function (gct) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                            $('#group_id').html(gct);
                        }]
                    })
                }else{
                    $("#lessontypeid").html("<option value=\"choose\">--Выберите тип занятия--</option>");
                    $("#group_id").html("<option value=\"choose\">--Выберите группу--</option>");
                    $("#selectedgroupstudents").empty();
                    $("#selectedgroupstudents2").empty();
                    $('#selectedgrouprating').empty();
                }
            });

            $('#group_id').on('change', changeData);
            $('#editdatabtn').click(changeData);
            $('#outputdatabtn').click(changeData);

            function changeData (){ // задаем функцию при нажатиии на элемент <button>
                var disid = $('#disid').val();
                var group_num = $('#group_id').val();

                if(group_num !== "choose"){
                    $.ajax({
                        method: "POST", // метод HTTP, используемый для запроса
                        url: "php_scripts/query_postdata/teacher/query_teacher_show_tables.php", // строка, содержащая URL адрес, на который отправляется запрос
                        data: { // данные, которые будут отправлены на сервер
                            disid: disid,
                            group_num: group_num
                        },
                        success: [
                            function(data) {
                                data = JSON.parse(data);

                                $('#lessontypeid').html(data.class_types);
                                $('#selectedgroupstudents').html(data.table1);
                                $('#selectedgroupstudents2').html(data.table2);
                                $('#selectedgrouprating').html(data.table3);
                            }]
                    })
                }else{
                    $("#lessontypeid").html("<option value=\"choose\">--Выберите тип занятия--</option>");
                    $("#selectedgroupstudents").empty();
                    $("#selectedgroupstudents2").empty();
                    $('#selectedgrouprating').empty();
                }
            }

            $('#acceptdatabtn').click(function(){ // задаем функцию при нажатиии на элемент <button>
                var disid = $('#disid').val();
                var group_num = $('#group_id').val();
                var lessontypeid = $('#lessontypeid').val();
                var date = $('#date').val();

                var studs = [];
                $('.studentsmark1').each(function () {
                    studs.push($(this).val());
                });

                if(lessontypeid !== "choose" && date !== ""){
                    $.ajax({
                        method: "POST", // метод HTTP, используемый для запроса
                        url: "php_scripts/query_postdata/teacher/query_teacher_add_point.php", // строка, содержащая URL адрес, на который отправляется запрос
                        data: { // данные, которые будут отправлены на сервер
                            disid: disid,
                            group_num: group_num,
                            lessontypeid: lessontypeid,
                            date: date,
                            studs: studs
                        },
                        success: [function () { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                            alert("Данные успешно переданы");
                        }]
                    })
                }else{
                    $("#lessontypeid").val("choose");
                    $("#date").val("");
                    $('.studentsmark1').each(function () {
                        $(this).val(0);
                    });
                    alert("Выберите данные");
                }
            });

            $('#acceptdatabtn2').click(function(){ // задаем функцию при нажатиии на элемент <button>

                var disid = $('#disid').val();
                var group_num = $('#group_id').val();

                var dates = [];
                var types = [];
                var points = [];

                $('.editdate2').each(function(){
                    dates.push($(this).val());
                });
                $('.edittype2').each(function(){
                    types.push($(this).val());
                });
                $('.point_table2').each(function(){
                    points.push($(this).val());
                });

                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/teacher/query_teacher_edit_table2.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        disid: disid,
                        group_num: group_num,
                        dates: dates,
                        types: types,
                        points: points
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
    <div id="teacherpage">
        <h2 class="teacherFIO">
			<?php
				if(!is_null($teacher['patronymic_teach'])){
					echo $teacher['surname_teach']." ".$teacher['name_teach']." ".$teacher['patronymic_teach'];
				} else {
					echo $teacher['surname_teach']." ".$teacher['name_teach'];
				}
			?>
		</h2>
		<h3 class="departmentname"><?php echo $teacher['name_depart'] ?></h3>
        <h4 class="lessonname">Учебная дисциплина:</h4>
        <div>
            <select class="disciplinename" id="disid">
                <option value="choose">--Выберите дисциплину--</option>
                <?php
                    global $groupInfo;
                    getTeachPlan($teacher['id_teach']);

                    for($i=0; $i<count($teacher_dis); $i++){
                        $dis_id = $teacher_dis[$i]['dis_id'];
                        $dis_name = $teacher_dis[$i]['dis_name'];
                        echo "<option value='$dis_id'>".$dis_name."</option>";
                    }
                ?>
            </select>
        </div>

        <h4 class="groupnumber">Номер группы:</h4>
        <div>
            <select class="group" id="group_id">
                <option value="choose">--Выберите группу--</option>
            </select>
        </div>

        <button id="enterdatabtn" class="tabl" onclick="opendata(event, 'enterdata')"><h3>Ввести данные в таблицу</h3></button>
        <button id="editdatabtn" class="tabl" onclick="opendata(event, 'editdata')"><h3>Редактировать таблицу</h3></button>
        <button id="outputdatabtn" class="tabl" onclick="opendata(event, 'outputdata')"><h3>Вывести рейтинг по группе</h3></button>

        <div id="enterdata" class="tabc" style="display: none">
            <h4 class="typeoflesson">Тип занятия:</h4>
            <div>
                <select class="lessontype" id="lessontypeid">
                    <option value="choose">--Выберите тип занятия--</option>
                </select>
            </div>
            <h4 class="lessondate">Дата:</h4>
            <div>
                <input type="date" name="calendar" id="date">
            </div>

            <table id="selectedgroupstudents"></table>
            <button id="acceptdatabtn">Применить</button>
        </div>


        <div id="editdata" class="tabc" style="display: none">
            <div class="scroll">
                    <table id="selectedgroupstudents2"></table>
            </div>
            <button id="acceptdatabtn2">Применить</button>
        </div>


        <div id="outputdata" class="tabc" style="display: none">
            <table id="selectedgrouprating"></table>
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