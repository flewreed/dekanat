<?php
	require 'php_scripts/session.php';
	require 'php_scripts/guide_data.php';
	$departments = getDepartments();
	$disciplines = getDisciplines();
	$teachers = getTeachers();
	$students = getStudents();
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
	<title>Справочник</title>

	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$('.remove').on('click', removeStudent);

			// УДАЛИТЬ СТУДЕНТА
			function removeStudent (){ // задаем функцию при нажатиии на элемент <button>
				if (confirm("Вы хотите удалить студента?")) {
					var id_stud = $(this).val();
					$.ajax({
						method: "POST", // метод HTTP, используемый для запроса
						url: "php_scripts/query_postdata/guide/query_removestudent.php", // строка, содержащая URL адрес, на который отправляется запрос
						data: { // данные, которые будут отправлены на сервер
							id_stud: id_stud
						},
						success: [
							function(data) {
								data = JSON.parse(data);
								alert(data.msg);
								$('#studenslist').html(data.new_table)
							}]
					})
				}
			}
		});
	</script>
</head>
<body onload="document.getElementById('defaultOpen').click();"> <!-- по умолчанию открывается подстраница с преподавателями -->
<div id="header">
	<img src="images/logo.png" alt="Логотип" width="130px"/>
	<h1><b>Балльно-рейтинговая система</b></h1>
	<h3>Санкт-Петербургский государственный технологический институт</h3>

	<a href="?logout">
		<Button id="exitbutton"><h3>Выйти</h3></Button>
	</a>
</div>

<div id="container">
    <div id="selectguide">
        <div class="tab">
            <button class="tablinks active" onclick="student_openwindow(event, 'teachers')" id="defaultOpen"><h3>Преподаватели</h3></button>
            <button class="tablinks" onclick="student_openwindow(event, 'students')"><h3>Студенты</h3></button>
            <button class="tablinks" onclick="student_openwindow(event, 'departments')"><h3>Кафедры</h3></button>
            <button class="tablinks" onclick="student_openwindow(event, 'disciplines')"><h3>Дисциплины</h3></button>
        </div>

        <!--ТАБЛИЦЫ-->

        <!--ПРЕПОДАВАТЕЛИ-->

        <div id="teachers" class="tabcontent">
            <form action="" method="get">
                <input id="searchinput" placeholder="Поиск..." type="search" onkeyup="searchteacher()">
            </form>

            <table id="teacherslist">
				<?php
					for($i = 0; $i < count($teachers); $i++) {
						echo	"<tr>
									<td>";
						echo			"<img src=".$teachers[$i]['photo_teach']." id=\"teacherphoto\">";
						if(!is_null($teachers[$i]['patronymic_teach'])){
							echo 		"<h3 id=\"teachername\">".$teachers[$i]['surname_teach']." ".$teachers[$i]['name_teach']." ".$teachers[$i]['patronymic_teach']."</h3>";
						} else {
							echo 		"<h3 id=\"teachername\">".$teachers[$i]['surname_teach']." ".$teachers[$i]['name_teach']."</h3>";
						}
						$post = "";
						if (count($teachers[$i]['post']) !== 0){
							$k = 0;
							for ($k ; $k < (count($teachers[$i]['post']))-1 ;$k++){
								$post = $post.$teachers[$i]['post'][$k].", ";
							}
							$post = $post.$teachers[$i]['post'][$k];
							echo 		"<h4 id=\"post\">".$post."</h4>";
						}
						
						
						$degree = "";
						if (count($teachers[$i]['degree']) !== 0){
							$k = 0;
							for ($k ; $k < (count($teachers[$i]['degree']))-1 ;$k++){
								$degree = $degree.$teachers[$i]['degree'][$k].", ";
							}
							$degree = $degree.$teachers[$i]['degree'][$k];
							
							echo 		"<h4 id=\"grade\">".$degree."</h4>";
						}
						
						echo 			"<h4 id=\"contacts\">Контактные данные: </h4>";
						$count_cont = 0;
						if (!is_null($teachers[$i]['current_phone_teach'])) {
							$phone = "8 (".mb_substr($teachers[$i]['current_phone_teach'], 0, 3).") "
								.mb_substr($teachers[$i]['current_phone_teach'], 3, 3)."-"
								.mb_substr($teachers[$i]['current_phone_teach'], 6,2)."-"
								.mb_substr($teachers[$i]['current_phone_teach'], 8);
							
							if (!is_null($teachers[$i]['internal_phone']) || !is_null($teachers[$i]['current_mail_teach'])) {
								echo 	"<p class=\"teacherphone contact\">".$phone.",</p>";
							} else {
								echo 	"<p class=\"teacherphone contact\">".$phone."</p>";
							}
							$count_cont++;
						}
						if (!is_null($teachers[$i]['internal_phone'])) {
							if (!is_null($teachers[$i]['current_mail_teach'])) {
								echo 	"<p class=\"teacherphone contact\">вн. тел. ".$teachers[$i]['internal_phone'].",</p>";
							} else {
								echo 	"<p class=\"teacherphone contact\">вн. тел. ".$teachers[$i]['internal_phone']."</p>";
							}
							$count_cont++;
						}
						if (!is_null($teachers[$i]['current_mail_teach'])) {
							echo		"<p class=\"teachermail contact\">".$teachers[$i]['current_mail_teach']."</p>";
							$count_cont++;
						}
						if ($count_cont === 0) {
							echo 		"<p class=\"teacherphone contact\">не указаны</p>";
						}
						
						echo 		"</td>
								</tr>";
						}
					?>
            </table>
        </div>
		
        <!--СТУДЕНТЫ-->

        <div id="students" class="tabcontent">
            <form action="" method="get">
                <input id="searchinput3" placeholder="Поиск..." type="search" onkeyup="searchstudent()">
            </form>
            <a href="addstudent.php">
                <button id="addstudent">Добавить студента</button>
            </a>
            <table id="studenslist">
				<?php
					for ($i = 0; $i < count($students); $i++) {
						echo 	"<tr class=\"student\">
                    				<td class=\"student\">";
						echo		 	"<form action=\"editstudent.php\" method=\"post\" class='edit1'>
											<a href='editstudent.php' class='edit1'><button id=\"editstudent\" name='stud_id' value='".$students[$i]['id_stud']."'>Редактировать</button></a>
										</form>";
                        echo 			"<img src=".$students[$i]['photo_stud']." id=\"studentphoto\">";
						if(!is_null($students[$i]['patronymic_stud'])){
							echo 		"<h3 id=\"studentname\">".$students[$i]['surname_stud']." ".$students[$i]['name_stud']." ".$students[$i]['patronymic_stud']."</h3>";
						} else {
							echo 		"<h3 id=\"studentname\">".$students[$i]['surname_stud']." ".$students[$i]['name_stud']."</h3>";
						}
                        echo            "<button id=\"deletestudent\" class='edit1 remove' value='".$students[$i]['id_stud']."'>Удалить</button>";
                        echo 			"<h4>Направление: </h4>";
						echo			"<p id=\"major\">".$students[$i]['code_major']." ".$students[$i]['name_major']."</p>";
                        echo			"<h4>Номер группы: </h4>";
						echo			"<p id=\"group\">".$students[$i]['group_number']."</p>";
						echo			"<h4>Номер зачетной книжки: </h4>";
						echo			"<p id=\"gradebook\">".$students[$i]['gradebook_number']."</p>";
						echo			"<h4>Гражданство: </h4>";
						echo			"<p id=\"country\">".$students[$i]['country']."</p>";
						echo			"<h4>Форма обучения: </h4>";
						echo			"<p id=\"educationform\">".$students[$i]['education_stage']." (".$students[$i]['education_form'].", ".$students[$i]['group_year'].")</p>";
						echo			"<h4>Тип финансирования: </h4>";
						echo			"<p id=\"financingtype\">".$students[$i]['education_basis']." (".$students[$i]['addmission_type'].")</p>";
						echo			"<h4>Контактные данные: </h4>";
						
						$count_cont = 0;
						if (!is_null($students[$i]['phone'])) {
							$phone = "8 (".mb_substr($students[$i]['phone'], 0, 3).") "
								.mb_substr($students[$i]['phone'], 3, 3)."-"
								.mb_substr($students[$i]['phone'], 6,2)."-"
								.mb_substr($students[$i]['phone'], 8);
							if (!is_null($students[$i]['mail'])) {
								echo 	"<p class=\"studentphone contact\">".$phone.",</p>";
							} else {
								echo 	"<p class=\"studentphone contact\">".$phone."</p>";
							}
							$count_cont++;
						}
						if (!is_null($students[$i]['mail'])) {
							echo		"<p class=\"studentemail contact\">".$students[$i]['mail']."</p>";
							$count_cont++;
						}
						if ($count_cont === 0) {
							echo 		"<p class=\"teacherphone contact\">не указаны</p>";
						}
						echo 		"</td>
                				</tr>";
					}
				?>
            </table>
        </div>

        <!--КАФЕДРЫ-->

        <div id="departments" class="tabcontent">
            <form action="" method="get">
                <input id="searchinput1" placeholder="Поиск..." type="search" onkeyup="searchdepartment()">
            </form>

            <table id="departmentslist">
				<?php
					for($i = 0; $i < count($departments); $i++) {
						echo "<tr>
                    			<td><h4 id=\"departmentname\">".$departments[$i]."</h4></td>
                			</tr>";
					}
				?>
            </table>
        </div>

        <!--ДИСЦИПЛИНЫ-->

        <div id="disciplines" class="tabcontent">
            <form action="" method="get">
                <input id="searchinput2" placeholder="Поиск..." type="search" onkeyup="searchdiscipline()">
            </form>

            <table id="disciplineslist">
				<?php
					for($i = 0; $i < count($disciplines); $i++) {
						echo "<tr>
                    			<td><h4 id=\"disciplinename\">".$disciplines[$i]."</h4></td>
                			</tr>";
					}
				?>
            </table>
        </div>
    </div>
</div>
<div id="footer">
	<p> &copy; 2022, Факультет информационных технологий и управления,<br>
		Санкт-Петербургский государственный технологический институт<br>
		(Технический университет)</p>
</div>
<script type="text/javascript" src="js_scripts/student_openwindow.js"></script>
<script type="text/javascript" src="js_scripts/backbtn.js"></script>
<script type="text/javascript" src="js_scripts/search.js"></script>
</body>
</html>