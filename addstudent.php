<?php
	require 'php_scripts/session.php';
	require 'php_scripts/guide_data.php';
	
	$ad_type = getAddmissionType(); //поступление
	$ed_basis = getEducationBasis(); //финансирование
	$years = getAddmissionYears();
	$subdivisions = getSubdivisions();
	$ed_form = getEducationForm();
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
	<title>Информация о студенте</title>

	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#ad_type_id").val('1');
			$("#ed_basis_id").val('1');
			
			$('#subdivision_id').on('change', changeSubdivision);
			
			$('#depart_id').on('change', changeDepart);

			$('#major_id').on('change', changeGroup);
			$('#ed_form').on('change', changeGroup);
			$('#course').on('change', changeGroup);
			
			$('#addstudbtn').on('click', addStudent);
			
			// КАФЕДРА
			function changeSubdivision (){ // задаем функцию при нажатиии на элемент <button>
				var subdivision_id = $('#subdivision_id').val();

				if (subdivision_id !== 'choose'){
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/guide/query_addstudent_depart.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							subdivision_id: subdivision_id
						},
						success: [
							function(data) {
                                $("#major_id").html("<option value=\"choose\">--Выберите направление--</option>");
                                $('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
								$('#depart_id').html(data);
							}]
					})
				} else {
					$("#depart_id").html("<option value=\"choose\">--Выберите кафедру--</option>");
					$("#major_id").html("<option value=\"choose\">--Выберите направление--</option>");
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}

			// НАПРАВЛЕНИЕ
			function changeDepart (){ // задаем функцию при нажатиии на элемент <button>
				var depart_id = $('#depart_id').val();
				if (depart_id !== 'choose'){
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/guide/query_addstudent_major.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							depart_id: depart_id
						},
						success: [
							function(data) {
								$('#major_id').html(data);
                                $('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
							}]
					})
				} else {
					$("#major_id").html("<option value=\"choose\">--Выберите направление--</option>");
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}

			// ГРУППА
			function changeGroup (){ // задаем функцию при нажатиии на элемент <button>
				var major_id = $('#major_id').val();
				var ed_form = $('#ed_form').val();
				var course = $('#course').val();
				if (major_id !== 'choose' && ed_form !== 'choose'){
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/guide/query_addstudent_group.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							major_id: major_id,
							ed_form: ed_form,
							course: course
						},
						success: [
							function(data) {
								$('#group_id').html(data);
							}]
					})
				} else {
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}
			
			function addStudent() {
				var student = {
					surname: $('#addstudsurname').val(),
					name: $('#addstudname').val(),
					patronymic: $('#addstudpatronymic').val(),
					date_of_birth: $('#addstudbirthdate').val(),
					gender: $('#addstudgeneder').val(),
					email: $('#addstudemail').val(),
					phone: $('#addstudphone').val().slice(3, 6) + $('#addstudphone').val().slice(7, 10) + $('#addstudphone').val().slice(11, 13) + $('#addstudphone').val().slice(14, 16),
					ad_type_id: $('#ad_type_id').val(),
					ed_basis_id: $('#ed_basis_id').val(),
					ad_year: $('#year').val(),
					gradebook_num: $('#addstudgradebook').val()
				};
				
				var address = [
					{
						type_address: 2,
						country: $('#rescountry').val(),
						region: $('#resregion').val(),
						city: $('#reslocality').val(),
						street: $('#resstreet').val(),
						house: $('#reshousenumber').val(),
						flat: $('#resapartnumber').val()
					},
					{
						type_address: 1,
						country: $('#regcountry').val(),
						region: $('#regregion').val(),
						city: $('#reglocality').val(),
						street: $('#regstreet').val(),
						house: $('#reghousenumber').val(),
						flat: $('#regapartnumber').val()
					}
				];

				var parents = [
					{
						surname: $('#parsurname').val(),
						name: $('#parname').val(),
						patronymic: $('#parpatronymic').val(),
						job: $('#parjobplace').val(),
						phone: $('#parphone').val().slice(3, 6) + $('#parphone').val().slice(7, 10) + $('#parphone').val().slice(11, 13) + $('#parphone').val().slice(14, 16)
					},
					{
						surname: $('#parsurname2').val(),
						name: $('#parname2').val(),
						patronymic: $('#parpatronymic2').val(),
						job: $('#parjobplace2').val(),
						phone: $('#parphone2').val().slice(3, 6) + $('#parphone2').val().slice(7, 10) + $('#parphone2').val().slice(11, 13) + $('#parphone2').val().slice(14, 16)
					}
				];

				var stud_json = JSON.stringify(student);
				var address_json = JSON.stringify(address);
				var parents_json = JSON.stringify(parents);
				
				var group_id = $('#group_id').val();
				var group_num = $('#group_id option:selected').text();

				$.ajax({
					method: "POST", // метод HTTP, используемый для запроса
					url: "php_scripts/query_postdata/guide/query_addstudent.php", // строка, содержащая URL адрес, на который отправляется запрос
					data: { // данные, которые будут отправлены на сервер
						stud_json: stud_json,
						address_json: address_json,
						parents_json: parents_json,
						group_id: group_id,
						group_num: group_num
					},
					success: [
						function(msg) {
							alert(msg);
							window.location.replace("guide.php");
						}]
				})
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
	
	<div id="addstudentinfo">
		<h4>Фамилия:</h4>
		<input type="text" id="addstudsurname">
		<br>
		<h4>Имя:</h4>
		<input type="text" id="addstudname">
		<br>
		<h4>Отчество:</h4>
		<input type="text" id="addstudpatronymic">
		<br>
		<h4>Дата рождения:</h4>
		<input type="date" id="addstudbirthdate">
		<br>
		<h4>Пол:</h4>
		<select id="addstudgeneder" class="addstudentrytype">
			<option value="м">муж.</option>
			<option value="ж">жен.</option>
		</select>
		<br>
		<h4>Электронная почта:</h4>
		<input type="email" id="addstudemail">
		<br>
		<h4>Контактный телефон:</h4>
		<input id="addstudphone" type="tel"
			   pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
			   placeholder="+7(___)___-__-__">
		<br><br>
		<h4>Тип поступления:</h4>
		<div>
			<select id="ad_type_id" class="addstudentrytype">
				<?php
					for ($i = 0; $i < count($ad_type); $i++) {
						echo "<option value='".$ad_type[$i]['id_addmission_type']."'>".$ad_type[$i]['name_addmission_type']."</option>";
					}
				?>
			</select>
		</div>
		<h4>Тип финансирования:</h4>
		<div>
			<select id="ed_basis_id" class="addstudfintype">
				<?php
					for ($i = 0; $i < count($ed_basis); $i++) {
						echo "<option value='".$ed_basis[$i]['id_education_basis']."'>".$ed_basis[$i]['name_education_basis']."</option>";
					}
				?>
			</select>
		</div>
		<h4>Год поступления:</h4>
		<div>
			<select id="year" class="addstudyear">
				<option value="choose">--Выберите год поступления--</option>
				<?php
					for ($i = 0; $i < count($years); $i++) {
						echo "<option value='".$years[$i]."'>".$years[$i]."</option>";
					}
				?>
			</select>
		</div>
		<h4>Курс:</h4>
		<div>
			<select id="course" class="addstudcourse">
				<?php
					for ($i = 0; $i<5; $i++){
						echo "<option value='".($i + 1)."'>".($i + 1)." курс</option>";
					}
				?>
			</select>
		</div>
		<h4>Факультет:</h4>
		<div>
			<select id="subdivision_id" class="addstudsubdivision">
				<option value="choose">--Выберите факультет--</option>
				<?php
					for ($i = 0; $i < count($subdivisions); $i++) {
						echo "<option value='".$subdivisions[$i]['id_subdivision']."'>".$subdivisions[$i]['name_subdivision']."</option>";
					}
				?>
			</select>
		</div>
		<h4>Кафедра:</h4>
		<div>
			<select id="depart_id" class="addstuddepart">
				<option value="choose">--Выберите кафедру--</option>
			</select>
		</div>
		<h4>Направление:</h4>
		<div>
			<select id="major_id" class="addstudmajor">
				<option value="choose">--Выберите направление--</option>
			</select>
		</div>
		<h4>Форма обучения:</h4>
		<div>
			<select id="ed_form" class="addstudeducationform">
				<?php
					for ($i = 0; $i < count($ed_form); $i++) {
						echo "<option value='".$ed_form[$i]['id_education_form']."'>".$ed_form[$i]['name_education_form']."</option>";
					}
				?>
			</select>
		</div>
		
		<h4>Группа:</h4>
		<div>
			<select id="group_id" class="addstudgroup">
				<option value="choose">--Выберите группу--</option>
			</select>
		</div>
		<h4>Номер зачётной книжки:</h4>
		<input type="text" id="addstudgradebook">
		<br>
		<br>

		<div id="residentialaddress">
			<h4>Адрес проживания</h4>
			<br>
			<br>
			<div id="resaddr">
				<h4>Страна:</h4>
				<input type="text" id="rescountry">
				<br>
				<h4>Регион:</h4>
				<input type="text" id="resregion">
				<br>
				<h4>Населенный пункт:</h4>
				<input type="text" id="reslocality">
				<br>
				<h4>Улица:</h4>
				<input type="text" id="resstreet">
				<br>
				<h4>Номер дома:</h4>
				<input type="text" id="reshousenumber">
				<br>
				<h4>Номер квартиры:</h4>
				<input type="text" id="resapartnumber">
				<br>
			</div>
		</div>
		<div id="registrationaddress">
			<h4>Адрес регистрации</h4>
			<br>
			<br>
			<div id="regaddr">
				<h4>Страна:</h4>
				<input type="text" id="regcountry">
				<br>
				<h4>Регион:</h4>
				<input type="text" id="regregion">
				<br>
				<h4>Населенный пункт:</h4>
				<input type="text" id="reglocality">
				<br>
				<h4>Улица:</h4>
				<input type="text" id="regstreet">
				<br>
				<h4>Номер дома:</h4>
				<input type="text" id="reghousenumber">
				<br>
				<h4>Номер квартиры:</h4>
				<input type="text" id="regapartnumber">
				<br>
			</div>
		</div>
		<div id="parentinfo">
			<h4>Сведения о родителе/опекуне</h4>
			<br>
			<br>
			<div id="parent">
				<h4>Фамилия:</h4>
				<input type="text" id="parsurname">
				<br>
				<h4>Имя:</h4>
				<input type="text" id="parname">
				<br>
				<h4>Отчество:</h4>
				<input type="text" id="parpatronymic">
				<br>
				<h4>Место работы:</h4>
				<input type="text" id="parjobplace">
				<br>
				<h4>Номер телефона:</h4>
				<input id="parphone" type="tel"
					   pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
					   placeholder="+7(___)___-__-__">
				<br>
			</div>
		</div>
		<div id="parentinfo2">
			<h4>Сведения о родителе/опекуне</h4>
			<br>
			<br>
			<div id="parent2">
				<h4>Фамилия:</h4>
				<input type="text" id="parsurname2">
				<br>
				<h4>Имя:</h4>
				<input type="text" id="parname2">
				<br>
				<h4>Отчество:</h4>
				<input type="text" id="parpatronymic2">
				<br>
				<h4>Место работы:</h4>
				<input type="text" id="parjobplace2">
				<br>
				<h4>Номер телефона:</h4>
				<input id="parphone2" type="tel"
					   pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
					   placeholder="+7(___)___-__-__">
				<br>
			</div>
		</div>

		<button id="addstudbtn" >Применить</button>
	</div>
</div>
<div id="footer">
	<p> &copy; 2022, Факультет информационных технологий и управления,<br>
		Санкт-Петербургский государственный технологический институт<br>
		(Технический университет)</p>
</div>
<script type="text/javascript" src="js_scripts/phone_mask.js"></script>
</body>
</html>