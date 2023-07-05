<?php
	require 'php_scripts/session.php';
	require 'php_scripts/elderstudent_data.php';
	getGroupInfo();

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
	<title>Старосте</title>

	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		$(document).ready(function(){

            $('#disciplinename').on('change', function(){ // задаем функцию при нажатиии на элемент <button>
				var disciplinename = $('#disciplinename').val();

				$.ajax({
					method: "POST", // метод HTTP, используемый для запроса
					url: "php_scripts/query_postdata/elderstudent/query_elderstudent_class_type.php", // строка, содержащая URL адрес, на который отправляется запрос
					data: { // данные, которые будут отправлены на сервер
						disciplinename: disciplinename,
					},
					success: [function (gct) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
						$('#typeoflesson').html(gct);
					}]
				})
			});

            $('#sendpasses').click(function(){ // задаем функцию при нажатиии на элемент <button>
                var disciplinename = $('#disciplinename').val();
                var date = $('#date').val();
                var typeoflesson = $('#typeoflesson').val();
                var checks = [];
                $('input.visiting:checkbox:checked').each(function () {
                    checks.push($(this).val());
                });
               if(disciplinename !== "choose" && date !== "" && typeoflesson !== "choose" && checks.length != 0){
                   $.ajax({
                       method: "POST", // метод HTTP, используемый для запроса
                       url: "php_scripts/query_postdata/elderstudent/query_elderstudent_add_miss.php", // строка, содержащая URL адрес, на который отправляется запрос
                       data: { // данные, которые будут отправлены на сервер
                           disciplinename: disciplinename,
                           date: date,
                           typeoflesson: typeoflesson,
                           checks: checks
                       },
                       success: [function (msg) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
                           alert("Данные успешно переданы");
                           window.location.href = 'elderstudent.php';
                       }]
                   })
               }else{
                   alert ("Вы не выбрали данные");
               }
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
	<div id="selescttypeanddate">
		<div style="height: 35px">
			<h4>Направление:&emsp;</h4>
			<p id="major">
				<?php
					echo $groupInfo['code_major']." ".$groupInfo['name_major'];
				?>
			</p>
		</div>
		<div style="height: 35px">
			<h4>Группа:&emsp;</h4>
			<p id="group">
				<?php
					echo $groupInfo['group_number'];
				?>
			</p>
		</div>

		<div>
			<h4>Учебная дисциплина:</h4>
			<div>
				<select id="disciplinename">
					<option value="choose">--Выберите дисциплину--</option>
					<?php
						require 'php_scripts/groupInfo.php';
						//	getDisciplinies($groupInfo['semester'], $groupInfo['group_number']);
						$disciplinies = getDisciplinies(4, $groupInfo['group_number']);
						for ($i = 0; $i < count($disciplinies); $i++) {
							$dis_value = $disciplinies[$i]['name_discipline'];
							echo "<option value='$dis_value'>". $dis_value ."</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div>
			<h4>Дата:</h4>
			<div>
				<input type="date" name="calendar" id="date">
			</div>
		</div>
		<h4>Тип занятия:</h4>
		<div>
			<select id="typeoflesson">
				<option value="choose">--Выберите тип занятия--</option>
			</select>
		</div>
	</div>

	<table id="listofpass">
		<tr>
			<th width="70%"><h4>ФИО студента</h4></th>
			<th><h4>Отсутствовал</h4></th>
		</tr>
		<?php
			getStudentsList($groupInfo['group_number']);
			for ($i = 0; $i < count($groupStudents); $i++) {
				echo "<tr>
						<td><p id=\"studentFIO\">".$groupStudents[$i]['surname_stud']." "
					.$groupStudents[$i]['name_stud']." ".$groupStudents[$i]['patronymic_stud']."</p></td>
						<td><p id=\"pass\"><input class='visiting' type=\"checkbox\" id='check[]' value='$i'</p></td>
					</tr>";
				
			}
		?>
	</table>

	<button id="sendpasses">Отправить</button>

</div>

<div id="footer">
	<p> &copy; 2022, Факультет информационных технологий и управления,<br>
		Санкт-Петербургский государственный технологический институт<br>
		(Технический университет)</p>
</div>

</body>
</html>