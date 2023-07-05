<?php
	require 'php_scripts/session.php';
	require 'php_scripts/guide_data.php';
	$stud_id = $_POST['stud_id'];
	
	$stud = getStudentById($stud_id);
	$group = getGroupInfoByStudId($stud_id);
	
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
	<title>Редактировать студента</title>

	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#addstudgeneder").val('<?php echo $stud['gender']?>');
			$("#ad_type_id").val('<?php echo $stud['id_ad_type']?>');
			$("#ed_basis_id").val('<?php echo $stud['id_ed_basis']?>');
			$("#year").val('<?php echo $stud['ad_year']?>');
			
			$("#course").val('<?php echo $group['course']?>');
			$("#ed_form").val('<?php echo $group['id_ed_form']?>');
			
			$("#subdivision_id").val('<?php echo $group['id_subdivision']?>');
			$('#subdivision_id').on('change', changedSubdivision);
			changedSubdivision(1);
			
			$('#depart_id').on('change', changedDepart);
			$('#major_id').on('change', changeGroup);
			$('#ed_form').on('change', changeGroup);
			$('#course').on('change', changeGroup);

			// КАФЕДРА
			function changedSubdivision (isFirst = 2){ // задаем функцию при нажатиии на элемент <button>
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
								if(isFirst === 1) {
									$("#depart_id").val('<?php echo $group['id_depart']?>');
									changedDepart(1);
								}
							}]
					})
				} else {
					$("#depart_id").html("<option value=\"choose\">--Выберите кафедру--</option>");
					$("#major_id").html("<option value=\"choose\">--Выберите направление--</option>");
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}
			

			// НАПРАВЛЕНИЕ
			function changedDepart (isFirst = 2){ // задаем функцию при нажатиии на элемент <button>
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
                                if(isFirst === 1) {
									$("#major_id").val('<?php echo $group['id_major']?>');
									changeGroup(1);
								}
							}]
					})
				} else {
					$("#major_id").html("<option value=\"choose\">--Выберите направление--</option>");
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}
			
			function changeGroup (isFirst = 2){ // задаем функцию при нажатиии на элемент <button>
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
								if(isFirst === 1) {
									$("#group_id").val('<?php echo $group['id_group']?>');
								}
							}]
					})
				} else {
					$('#group_id').html("<option value=\"choose\">--Выберите группу--</option>");
				}
			}

            $('#addstudbtn').click(editStudent);

            function editStudent() {
                var student = {
                    id_stud: <?php echo $stud_id?>,
                    surname_stud: $('#addstudsurname').val(),
                    name_stud: $('#addstudname').val(),
                    patronymic_stud: $('#addstudpatronymic').val(),
                    date_of_birth: $('#addstudbirthdate').val(),
                    gender: $('#addstudgeneder').val(),
                    mail: $('#addstudemail').val(),
                    phone: $('#addstudphone').val().slice(3, 6) + $('#addstudphone').val().slice(7, 10) + $('#addstudphone').val().slice(11, 13) + $('#addstudphone').val().slice(14, 16),
                    id_ad_type: $('#ad_type_id').val(),
                    id_ed_basis: $('#ed_basis_id').val(),
                    ad_year: $('#year').val(),
                    gradebook_number: $('#addstudgradebook').val()
                };

                var address = [
                    {
                        id_address: <?php echo $stud['address'][2]['id_address'] ?>,
                        type_address: 2,
                        name_country: $('#rescountry').val(),
                        oblast: $('#resregion').val(),
                        city: $('#reslocality').val(),
                        street: $('#resstreet').val(),
                        house: $('#reshousenumber').val(),
                        flat: $('#resapartnumber').val()
                    },
                    {
                        id_address: <?php echo $stud['address'][1]['id_address'] ?>,
                        type_address: 1,
                        name_country: $('#regcountry').val(),
                        oblast: $('#regregion').val(),
                        city: $('#reglocality').val(),
                        street: $('#regstreet').val(),
                        house: $('#reghousenumber').val(),
                        flat: $('#regapartnumber').val()
                    }
                ];

                var parents = [
                    {
                        id_parent: <?php
                                        if (isset($stud['parents'][0])) {
                                            echo $stud['parents'][0]['id_parent'];
                                        }else{
                                            echo -1;
                                        }
                                    ?>,
                        surname_parent: $('#parsurname').val(),
                        name_parent: $('#parname').val(),
                        patronymic_parent: $('#parpatronymic').val(),
                        job: $('#parjobplace').val(),
                        phone_parent: $('#parphone').val().slice(3, 6) + $('#parphone').val().slice(7, 10) + $('#parphone').val().slice(11, 13) + $('#parphone').val().slice(14, 16)
                    },
                    {
                        id_parent: <?php
                                        if (isset($stud['parents'][1])) {
                                            echo $stud['parents'][1]['id_parent'];
                                        }else{
                                            echo -1;
                                        }
                                    ?>,
                        surname_parent: $('#parsurname2').val(),
                        name_parent: $('#parname2').val(),
                        patronymic_parent: $('#parpatronymic2').val(),
                        job: $('#parjobplace2').val(),
                        phone_parent: $('#parphone2').val().slice(3, 6) + $('#parphone2').val().slice(7, 10) + $('#parphone2').val().slice(11, 13) + $('#parphone2').val().slice(14, 16)
                    }
                ];

                var stud_json = JSON.stringify(student);
                var address_json = JSON.stringify(address);
                var parents_json = JSON.stringify(parents);

                var group_id = $('#group_id').val();
                var group_num = $('#group_id option:selected').text();

                $.ajax({
                    method: "POST", // метод HTTP, используемый для запроса
                    url: "php_scripts/query_postdata/guide/query_editstudent.php", // строка, содержащая URL адрес, на который отправляется запрос
                    data: { // данные, которые будут отправлены на сервер
                        stud_json: stud_json,
                        address_json: address_json,
                        parents_json: parents_json,
                        group_id: group_id,
                        group_num: group_num
                    },
                    success: [
                        function(data) {
							alert(data);
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
		<input type="text" id="addstudsurname" value="<?php echo $stud['surname_stud']?>">
		<br>
		<h4>Имя:</h4>
		<input type="text" id="addstudname" value="<?php echo $stud['name_stud']?>">
		<br>
		<h4>Отчество:</h4>
		<input type="text" id="addstudpatronymic" value="<?php if (!is_null($stud['patronymic_stud'])) {echo $stud['patronymic_stud'];} ?>">
		<br>
		<h4>Дата рождения:</h4>
		<input type="date" id="addstudbirthdate" value="<?php echo $stud['date_of_birth']?>">
		<br>
		<h4>Пол:</h4>
		<select id="addstudgeneder" class="addstudentrytype">
			<option value="м">муж.</option>
			<option value="ж">жен.</option>
		</select>
		<br>
		<h4>Электронная почта:</h4>
		<input type="email" id="addstudemail" value="<?php echo $stud['mail']?>">
		<br>
		<h4>Контактный телефон:</h4>
        <input id="addstudphone" type="tel"
               pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
               placeholder="+7(___)___-__-__"
                value="<?php echo "+7(".mb_substr($stud['phone'], 0, 3).")"
            .mb_substr($stud['phone'], 3, 3)."-"
            .mb_substr($stud['phone'], 6,2)."-"
            .mb_substr($stud['phone'], 8);?>">
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
		<input type="text" id="addstudgradebook" value="<?php echo $stud['gradebook_number']?>">
		<br>
		<br>

		<div id="residentialaddress">
			<h4>Адрес проживания</h4>
			<br>
			<br>
			<div id="resaddr">
				<h4>Страна:</h4>
				<input type="text" id="rescountry" value="<?php echo $stud['address'][2]['name_country']?>">
				<br>
				<h4>Регион:</h4>
				<input type="text" id="resregion" value="<?php echo $stud['address'][2]['oblast']?>">
				<br>
				<h4>Населенный пункт:</h4>
				<input type="text" id="reslocality" value="<?php echo $stud['address'][2]['city']?>">
				<br>
				<h4>Улица:</h4>
				<input type="text" id="resstreet" value="<?php echo $stud['address'][2]['street']?>">
				<br>
				<h4>Номер дома:</h4>
				<input type="text" id="reshousenumber" value="<?php echo $stud['address'][2]['house']?>">
				<br>
				<h4>Номер квартиры:</h4>
				<input type="text" id="resapartnumber" value="<?php echo $stud['address'][2]['flat']?>">
				<br>
			</div>
		</div>
		<div id="registrationaddress">
			<h4>Адрес регистрации</h4>
			<br>
			<br>
			<div id="regaddr">
				<h4>Страна:</h4>
				<input type="text" id="regcountry" value="<?php echo $stud['address'][1]['name_country']?>">
				<br>
				<h4>Регион:</h4>
				<input type="text" id="regregion" value="<?php echo $stud['address'][1]['oblast']?>">
				<br>
				<h4>Населенный пункт:</h4>
				<input type="text" id="reglocality" value="<?php echo $stud['address'][1]['city']?>">
				<br>
				<h4>Улица:</h4>
				<input type="text" id="regstreet" value="<?php echo $stud['address'][1]['street']?>">
				<br>
				<h4>Номер дома:</h4>
				<input type="text" id="reghousenumber" value="<?php echo $stud['address'][1]['house']?>">
				<br>
				<h4>Номер квартиры:</h4>
				<input type="text" id="regapartnumber" value="<?php echo $stud['address'][1]['flat']?>">
				<br>
			</div>
		</div>
		<div id="parentinfo">
			<h4>Сведения о родителе/опекуне</h4>
			<br>
			<br>
			<div id="parent">
				<h4>Фамилия:</h4>
				<input type="text" id="parsurname" value="<?php if (!is_null($stud['parents'][0])) {echo $stud['parents'][0]['surname_parent'];} ?>">
				<br>
				<h4>Имя:</h4>
				<input type="text" id="parname" value="<?php if (!is_null($stud['parents'][0])) {echo $stud['parents'][0]['name_parent'];} ?>">
				<br>
				<h4>Отчество:</h4>
				<input type="text" id="parpatronymic" value="<?php if (!is_null($stud['parents'][0])) {if (!is_null($stud['parents'][0]['patronymic_parent'])) {echo $stud['parents'][0]['patronymic_parent'];}} ?>">
				<br>
				<h4>Место работы:</h4>
				<input type="text" id="parjobplace" value="<?php if (!is_null($stud['parents'][0])) {if (!is_null($stud['parents'][0]['job'])) {echo $stud['parents'][0]['job'];}} ?>">
				<br>
				<h4>Номер телефона:</h4>
                <input id="parphone" type="tel"
                       pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
                       placeholder="+7(___)___-__-__"
                       value="<?php  if (!is_null($stud['parents'][0])) {
                           echo "+7(".mb_substr($stud['parents'][0]['phone_parent'], 0, 3).")"
                               .mb_substr($stud['parents'][0]['phone_parent'], 3, 3)."-"
                               .mb_substr($stud['parents'][0]['phone_parent'], 6,2)."-"
                               .mb_substr($stud['parents'][0]['phone_parent'], 8);
                       }?>">
				<br>
			</div>
		</div>
		<div id="parentinfo2">
			<h4>Сведения о родителе/опекуне</h4>
			<br>
			<br>
			<div id="parent2">
				<h4>Фамилия:</h4>
				<input type="text" id="parsurname2" value="<?php if (isset($stud['parents'][1])) {echo $stud['parents'][1]['surname_parent'];} ?>">
				<br>
				<h4>Имя:</h4>
				<input type="text" id="parname2" value="<?php if (isset($stud['parents'][1])) {echo $stud['parents'][1]['name_parent'];} ?>">
				<br>
				<h4>Отчество:</h4>
				<input type="text" id="parpatronymic2" value="<?php if (isset($stud['parents'][1])) {if (!is_null($stud['parents'][1]['patronymic_parent'])) {echo $stud['parents'][1]['patronymic_parent'];}} ?>">
				<br>
				<h4>Место работы:</h4>
				<input type="text" id="parjobplace2" value="<?php if (isset($stud['parents'][1])) {if (!is_null($stud['parents'][1]['job'])) {echo $stud['parents'][1]['job'];}} ?>">
				<br>
				<h4>Номер телефона:</h4>
                <input id="parphone2" type="tel"
                       pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}"
                       placeholder="+7(___)___-__-__"
                       value="<?php  if (isset($stud['parents'][1])) {
                           echo "+7(".mb_substr($stud['parents'][1]['phone_parent'], 0, 3).")"
                               .mb_substr($stud['parents'][1]['phone_parent'], 3, 3)."-"
                               .mb_substr($stud['parents'][1]['phone_parent'], 6,2)."-"
                               .mb_substr($stud['parents'][1]['phone_parent'], 8);
                       }?>">
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