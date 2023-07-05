<?php
	require 'php_scripts/session.php';
	require 'php_scripts/deanery_data.php';
	$deaneryUser = getDeaneryUser();
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
	<title>Деканату</title>

	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		//находим группы по кафедре
		$(document).ready(function(){
			$('#subdivision_id').on('change', changeData);
			$('#course_id').on('change', changeData);
			function changeData (){ // задаем функцию при нажатиии на элемент <button>
				var subid = $('#subdivision_id').val();
				var course = $('#course_id').val();
				
				if (subid !== 'choose' && course !== 'choose') {
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/deanery/query_deanery_groups.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							subid: subid,
							course: course
						},
						success: [
							function(data) {
								$('#grouprating').empty();
								$('#disciplineprog').empty();
								$("#discipline_id").html("<option value=\"choose\">--Выберите дисциплину--</option>");
								$('#group_id').html(data);
							}]
					})
				} else {
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
					$("#discipline_id").html("<option value=\"choose\">--Выберите дисциплину--</option>");
					$('#grouprating').empty();
					$('#disciplineprog').empty();
				}
			}
		});

		//находим список группы, дисциплины и ведомость по ним
		$(document).ready(function(){
			$('#group_id').on('change', changeData);
			function changeData (){ // задаем функцию при нажатиии на элемент <button>
				var subid = $('#subdivision_id').val();
				var course = $('#course_id').val();
				var group_number = $('#group_id').val();

				if (subid !== 'choose' && course !== 'choose' && group_number !== 'choose') {
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/deanery/query_deanery_perform.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							subid: subid,
							course: course,
							group_number: group_number
						},
						success: [
							function(data) {
								$('#disciplineprog').empty();
								$('#grouprating').empty();
								$("#discipline_id").html("<option value=\"choose\">--Выберите дисциплину--</option>");
								data = JSON.parse(data);
								$('#discipline_id').html(data.disciplinies);
								$('#grouprating').html(data.pr_table);
							}]
					})
				} else {
					$('#discipline_id').html("<option value=\"choose\">--Выберите дисциплину--</option>");
					$('#grouprating').empty();
					$('#disciplineprog').empty();
				}
			}
		});

		//успеваемость
		$(document).ready(function(){
			$('#discipline_id').on('change', changeData);
			function changeData (){ // задаем функцию при нажатиии на элемент <button>
				var subid = $('#subdivision_id').val();
				var course = $('#course_id').val();
				var discipline_id = $('#discipline_id').val();
				var group_number = $('#group_id').val();

				if (subid !== 'choose' && course !== 'choose' && group_number !== 'choose' && discipline_id !== 'choose') {
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/deanery/query_deanery_dis_perform.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							discipline_id: discipline_id,
							group_number: group_number
						},
						success: [
							function(data) {
								$('#disciplineprog').empty();
								$('#disciplineprog').html(data);
							}]
					})
				} else {
					$('#disciplineprog').empty();
				}
			}
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
    <div id="deanerypage">
		
		
<!--		<o>This is o</o>-->
		
		
        <h2 id="deaneryFIO">
			<?php
				if(!is_null($deaneryUser['patronymic'])){
					echo $deaneryUser['surname']." ".$deaneryUser['name']." ".$deaneryUser['patronymic'];
				} else {
					echo $deaneryUser['surname']." ".$deaneryUser['name'];
				}
			?>
		</h2>
		
        <h4 class="subdivision">Факультет:</h4>
        <div>
            <select class="subdivisionname" id="subdivision_id">
                <option value="choose">--Выберите факультет--</option>
				<?php
					require 'php_scripts/guide_data.php';
					$subdivisions = getSubdivisions();
		
					for($i=0; $i<count($subdivisions); $i++){
						$subd_id = $subdivisions[$i]['id_subdivision'];
						$subd_name = $subdivisions[$i]['name_subdivision'];
						echo "<option value='$subd_id'>".$subd_name."</option>";
					}
				?>
            </select>
        </div>
        <h4 class="coursenumber">Курс:</h4>
        <div>
            <select class="course" id="course_id">
                <option value="choose">--Выберите курс--</option>
				<?php
					for ($i = 0; $i<5; $i++){
						echo "<option value='".($i + 1)."'>".($i + 1)." курс</option>";
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

        <button id="outputratingbtn" class="tabl" onclick="opendata(event, 'outputrating')"><h3>Вывести рейтинг по группе</h3></button>
        <button id="viewprogbtn" class="tabl" onclick="opendata(event, 'viewprog')"><h3>Просмотр успеваемости</h3></button>
		
		
		
<!--РЕЙТИН-->
        <div id="outputrating" class="tabc" style="display: none">
            <div class="scroll">
                <table id="grouprating"></table>
            </div>
        </div>

		
<!--УСПЕВАЕМОСТЬ-->
        <div id="viewprog" class="tabc" style="display: none">
            <h4 class="discipname">Дисциплина:</h4>
            <div>
                <select class="discipline" id="discipline_id">
                    <option value="choose">--Выберите дисциплину--</option>
                </select>
            </div>
            <div class="scroll">
                <table id="disciplineprog"></table>
            </div>
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