<?php
	require 'php_scripts/session.php';
	require 'php_scripts/student_data.php';
    require 'upload_photo.php';

	personalInfo();
	$currentSemester = $semester;

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
	<title>Студенту</title>
</head>
<body onload="document.getElementById('defaultOpen').click();"> <!-- по умолчанию открывается подстраница с успеваемостью в текущем семестре -->
	<div id="header">
		<img src="images/logo.png" alt="Логотип" width="130px" class="studimg"/>
		<h1><b>Балльно-рейтинговая система</b></h1>
		<h3>Санкт-Петербургский государственный технологический институт</h3>
	
		<a href="?logout">
			<Button id="exitbutton"><h3>Выйти</h3></Button>
		</a>
	</div>
	
	<div id="container">
	
	<!--ИНФОРМАЦИЯ О СТУДЕНТЕ-->
		<div id="studentinfo">

            <div id="studentph">
                <img id="img_photo" src="<?php
                $fileurl = $stud['photo_stud'];
                $gender="";
                if($stud['gender_stud'] === "м"){$gender = "m";}else{$gender = "f";}
                                            if(file_exists($fileurl)){
                                                echo $fileurl;
                                            }
                                            else{
                                                global $con;
                                                mysqli_query($con, "update stud set photo_stud = 'default.png' where s_id_user = ".$_SESSION['$user_id'].";");
                                                echo "images/default_".$gender.".png";
                                            }

                                         ?>" alt="Фото студента">
                <form id="addphoto" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" class = "file" multiple accept="image/png,image/jpeg">
                    <input type="submit" id ="uploadphotobtn" value="Отправить">
                </form>
            </div>
            <?php
            // если была произведена отправка формы
            if(isset($_FILES['file'])) {
                // проверяем, можно ли загружать изображение
                $check = can_upload($_FILES['file']);

                if($check === true){
                    // загружаем изображение на сервер
                    make_upload($_FILES['file']);
                }
            }
            ?>

			<h2 id="studentname">
				<?php
					if(!is_null($stud['patronymic_stud'])){
						echo $stud['surname_stud']." ".$stud['name_stud']." ".$stud['patronymic_stud'];
					} else {
						echo $stud['surname_stud']." ".$stud['name_stud'];
					}
				?>
			</h2>
			<br><br>
			<h4>Направление:</h4><p id="major">
				<?php
					echo $stud['code_major']." ".$stud['name_major'];
				?>
			</p>
			<h4>Группа:</h4><p id="group">
				<?php
					echo $stud['group_number'];
				?>
			</p>
			<h4>Номер зачётной книжки:</h4><p id="gradebook_number">
				<?php
					echo $stud['gradebook_number'];
				?>
			</p>
		</div>
		
		<div id="studentperformance">
	
			<h3 id="selectsemester">Выберите семестр:</h3>
			<form method="POST">
				<?php
					$i = 0;
					for($i = 0; $i < $semester - 1; $i++) {
						echo "<button class=\"semesterbtn\" name=\"sem\" value=\"".($i+1)."\" onclick=\"chooseSemester(".($i+1).")\"><h3>".($i+1)."</h3></button>";
					}
					echo "<button class=\"semesterbtn\" name=\"sem\" value=\"".($i+1)."\" onclick=\"chooseSemester(".($i+1).")\"><h3>".($i+1)."</h3></button>";
				?>
			</form>
			
			<?php
				if(!empty($_POST['sem'])){
					$semester = $_POST['sem'];
				}
				
			?>
			<p id="semesterP" style="display: none"><?php echo $semester;?></p>
			
			<div class="tab">
				<button class="tablinks active" onclick="student_openwindow(event, 'certification')" id="defaultOpen"><h3>Аттестация</h3></button>
				<button class="tablinks" onclick="student_openwindow(event, 'performance')"><h3>Успеваемость</h3></button>
				<button class="tablinks" onclick="student_openwindow(event, 'attendance')"><h3>Посещаемость</h3></button>
			</div>
			
			<!--ТАБЛИЦЫ-->
	
			<!--АТТЕСТАЦИЯ-->
	
			<div id="certification" class="tabcontent">
				<table>
					<tr>
						<th width="60%"><h4>Учебная дисциплина</h4></th>
						<th><h4>Вид аттестации</h4></th>
						<th><h4>Итого баллов</h4></th>
						<th><h4>Оценка</h4></th>
					</tr>
					<!--Вывод дисциплин-->
					<?php
						$totalScore = 0;
						$attestation = getAttestation($semester);

						// !!! < длины массива
						for ($i = 0; $i < count($attestation); $i++){
							echo 	"<tr>";
							echo		"<td><p id=\"disciplinename\">".$attestation[$i]['name_discipline']."</p></td>";
							echo		"<td><p id=\"typeofcertification\">".$attestation[$i]['name_attestation']."</p></td>";
							echo		"<td><p id=\"scores\">".$attestation[$i]['points']."</p></td>";

							$attest;
							$month = date('n');

							if ($semester == $currentSemester
								&& (($month < 12 && $semester % 2 === 1) || ($month < 7 && $semester % 2 === 0))) {
								$attest = "-";
							} else {
								if ($attestation[$i]['points'] < 60) {$attest = 2;}
								elseif ($attestation[$i]['points'] >= 60 && $attestation[$i]['points'] <= 74) {$attest = 3;}
								elseif ($attestation[$i]['points'] >= 75 && $attestation[$i]['points'] <= 84) {$attest = 4;}
								elseif ($attestation[$i]['points'] >= 85) {$attest = 5;}
							}
							echo 		"<td><p id=\"scores\">".$attest."</p></td>";
							echo 	"</tr>";

							$totalScore += $attestation[$i]['points'];
						}
					?>
					<tr>
						<td colspan="2" style="text-align:left"> <h4>Рейтинг по дисциплинам:</h4></td>
						<td colspan="2" id="totalscore" title="Общий рейтинг/Место в группе">
							<?php
								echo $totalScore."/";
								for ($i = 0; $i < count($groupStudentsRating); $i++) {
									if ($groupStudentsRating[$i]['user_id'] === $_SESSION['$user_id']) {
										echo $groupStudentsRating[$i]['rating'];
									}
								}
							?>
						</td>
					</tr>
				</table>
			</div>
			
			<!--УСПЕВАЕМОСТЬ-->

			<div id="performance" class="tabcontent">
				<?php
					$perform = getPerform($semester);
					
					// вывод дисциплин
					for ($i = 0; $i < count($attestation); $i++){
						echo 	"<h3 id=\"disciplinename\">".$attestation[$i]['name_discipline']."
									<button class=\"openscorebtn\" onclick=\"openTable(event, 'disciplinescore_".$i."')\">
										<h2>+</h2>
									</button>
								</h3>";
						
						// вывод оценок
						$find_perform = false;
						for ($k = 0; $k < count($perform); $k++){
							if (!mb_strpos($perform[$k]['name_attestation'], "Курс")) {
								if($perform[$k]['name_discipline'] === $attestation[$i]['name_discipline']
									&& $perform[$k]['name_attestation'] === $attestation[$i]['name_attestation']){
									if(!$find_perform) {
										echo 	"<table class=\"disciplinescore\" id=\"disciplinescore_".$i."\" style=\"display: none\">
													<tr>
														<th width=\"50%\"><h4>Вид занятия</h4></th>
														<th width=\"30%\"><h4>Дата</h4></th>
														<th><h4>Балл</h4></th>
													</tr>";
										$find_perform = true;
									}
									echo 			"<tr>
														<td><p id=\"typeoflesson\">".$perform[$k]['class_type']."</p></td>
														<td><p id=\"lessondate\">".date_format(date_create($perform[$k]['date_perform']), 'd.m.Y')."</p></td>
														<td><p id=\"lessonscore\">".$perform[$k]['points']."</p></td>
													</tr>";
								}
							}
						}
						if ($find_perform) {echo "</table>";}
					}
				?>
			</div>
			
			<!--ПРОПУСКИ-->
			
			<div id="attendance" class="tabcontent">
				<table>
					<tr>
						<th width="70%"><h4>Учебная дисциплина</h4></th>
						<th><h4>Итого пропусков</h4></th>
					</tr>
					<?php
						for ($i = 0; $i < count($attestation); $i++){
							echo "<tr>
									<td><p id=\"disciplinename\">".$attestation[$i]['name_discipline']."</p></td>
									<td><p id=\"attendancecount\">".$attestation[$i]['passes']."</p></td>
								</tr>";
						}
					?>
				</table>
			</div>
		</div>
	</div>
	<?php
		if (isset($_GET['logout']))
		{
			logout();
		}
	?>
	
	<div id="footer">
		<p> &copy; 2022, Факультет информационных технологий и управления,<br>
			Санкт-Петербургский государственный технологический институт<br>
			(Технический университет)</p>
	</div>
	<script type="text/javascript" src="js_scripts/student_openwindow.js"></script>
	<script type="text/javascript" src="js_scripts/student_opentable.js"></script>
	<script type="text/javascript" src="js_scripts/backbtn.js"></script>

</body>
</html>