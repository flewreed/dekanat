<?php
	//query_editstudent.php
	//подключение файлов
    require '../../guide_data.php';

    //получение данных из форм в формате json
    $stud_json = $_POST['stud_json'];
    $address_json = $_POST['address_json'];
    $parents_json = $_POST['parents_json'];

    $group_id = $_POST['group_id'];
    $group_num = $_POST['group_num'];
	
	//преобразование json в массивы
    $stud = json_decode($stud_json, true);
    $address = json_decode($address_json, true);
    $parents = json_decode($parents_json, true);
	
	//проверка на пустые формы
	if ($stud['surname_stud'] === "" || $stud['name_stud'] === "" || $stud['mail'] === ""
		|| $stud['phone'] === "" || $stud['ad_year'] === "choose"
		|| $stud['gradebook_number'] === "" || $stud['date_of_birth'] === "") {
		echo "Данные о студенте не заполнены";
		exit(0);
	}
	
	//проверка корректности указанного телефона
	if (mb_strpos($stud['phone'], "_") !== false){
		echo "Телефон студента указан некорректно";
		exit(0);
	}
	
	//проверка корректности указанного email
	if (filter_var($stud['mail'], FILTER_VALIDATE_EMAIL) === false){
		echo "Электронная почта студента указана некорректно";
		exit(0);
	}
	
	//проверка выбранной группы
	if ($group_id === "choose") {
		echo "Группа не выбрана";
		exit(0);
	}
	
	//проверка на пустые формы
	for ($i = 0; $i < count($address); $i++) {
		if ($address[$i]['name_country'] === "" || $address[$i]['oblast'] === "" || $address[$i]['city'] === ""
			|| $address[$i]['street'] === "" || $address[$i]['house'] === "") {
			echo "Адрес не указан";
			exit(0);
		}
	}
	
	//проверка на корректное заполнение данных о родителях
	for ($i = 0; $i < count($parents); $i++) {
		if (($parents[$i]['surname_parent'] !== "" || $parents[$i]['name_parent'] !== "" || $parents[$i]['patronymic_parent'] !== ""
				|| $parents[$i]['job'] !== "" || $parents[$i]['phone_parent'] !== "")
			&& ($parents[$i]['surname_parent'] === "" || $parents[$i]['name_parent'] === "" || $parents[$i]['phone_parent'] === "")) {
			echo "Данные о родителях не указаны полностью";
			exit(0);
		}
		if ($parents[$i]['surname_parent'] !== "") {
			if (mb_strpos($parents[$i]['phone_parent'], "_") !== false){
				echo "Телефоны родителей указаны некорректно";
				exit(0);
			}
		}
	}

	//инициализация массива данных о студенте и их получение
    $stud_db = getStudentById($stud['id_stud']);

	//получение id группы студента
    $group = getGroupInfoByStudId($stud['id_stud']);
    $group_id_db = $group['id_group'];

    //инициализация массива адресов студента и их получение
    $address_db = getAddressByStudId($stud['id_stud']);

    //инициализация массивов ключей для сопоставления с бд
    $params_stud = array(
        "id_stud" => "id_stud",
        "surname_stud" => "surname_stud",
        "name_stud" => "name_stud",
        "patronymic_stud" => "patronymic_stud",
        "date_of_birth" => "date_of_birth",
        "gender_stud" => "gender",
        "current_phone_stud" => "phone",
        "current_mail_stud" => "mail",
        "id_addmission_type" => "id_ad_type",
        "id_education_basis" => "id_ed_basis",
        "addmission_year" => "ad_year",
        "gradebook_number" => "gradebook_number"
    );

    $params_parent = array(
        "id_parent" => "id_parent",
        "surname_parent" => "surname_parent",
        "name_parent" => "name_parent",
        "patronymic_parent" => "patronymic_parent",
        "current_phone_parent" => "phone_parent",
        "job" => "job",
    );

    $params_address = array(
        "id_address" => "id_address",
        "id_addr_country" => "name_country",
        "oblast" => "oblast",
        "city" => "city",
        "street" => "street",
        "house" => "house",
        "flat" =>"flat",
    );

    //перебор ключей массива студента
    while($param = current($params_stud)){
        global $con;
        
        //при нахождении несовпадающих данных обновляем базу данных
        if($stud_db[$param] != $stud[$param]){
            if($stud[$param] == ""){
                $qry = "update kod.stud set ".key($params_stud)." = null where id_stud = ".$stud['id_stud'].";";
            }else{
                $qry = "update kod.stud set ".key($params_stud)." = '".$stud[$param]."' where id_stud = ".$stud['id_stud'].";";
            }
            mysqli_query($con, $qry);
        }
        next($params_stud);
    }

	//при совпадающем количестве родителей обновляем/удаляем данные
    if(count($stud_db['parents']) == count($parents) ){
        for($i=0; $i<2; $i++){
        	//если данные форма пустая, удаляем родителя
            if ($parents[$i]['surname_parent'] == "") {
                mysqli_query($con, "delete from kod.parent_stud where id_ps_parent = ".$stud_db['parents'][$i]['id_parent']." and id_ps_stud = ".$stud['id_stud'].";");
                mysqli_query($con, "delete from kod.parent where id_parent = ".$stud_db['parents'][$i]['id_parent'].";");
            }else{
                $j=0;
                
				//перебор ключей массива родителей
                foreach ($params_parent as $param){
	
					//при нахождении несовпадающих данных обновляем бд
                    if($parents[$i][$param] != $stud_db['parents'][$i][$param]){
                        $qry = "update kod.parent set ".array_keys($params_parent)[$j]." = '".$parents[$i][$param]."' where id_parent = ".$parents[$i]['id_parent'].";";
                    	mysqli_query($con, $qry);
                    }
                    $j++;
                }
            }
        }
        //при меньшем количестве родителей в бд добавляем/обновляем данные
    }elseif(count($stud_db['parents']) < count($parents)){
        for($i=0; $i<2; $i++){
        	//если родителя нет в бд, добавляем его
            if($parents[$i]['id_parent'] == -1 && $parents[$i]['surname_parent'] != ""){

                global $con, $params_parent;
                
                //запрос для нахождения нового id в бд
                $getParId = mysqli_query($con, "select id_parent from kod.parent order by id_parent desc limit 1");
                $parents[$i]['id_parent'] = mysqli_fetch_assoc($getParId)['id_parent']+1;
                
                //запрос на добавление id родителя
                mysqli_query($con,"insert into kod.parent (id_parent) values (".$parents[$i]['id_parent'].")");
                
                //запрос на добавление связи студента и родителя
                mysqli_query($con,"insert into kod.parent_stud (id_ps_parent, id_ps_stud) values
										(".$parents[$i]['id_parent'].",".$stud['id_stud'].")");

                $j=0;
                //добавление данных о созданном родителе
                foreach($params_parent as $par){
                    global $con;
                    if($par == 'id_parent'){ $j++; continue;}
                    if($parents[$i][$par] == ""){
                        $hp = "null";
                    }else{
                        $hp = "'".$parents[$i][$par]."'";
                    }
                    $qry = "update kod.parent set ".array_keys($params_parent)[$j]." = ".$hp." where id_parent = ".$parents[$i]['id_parent'].";";
                    mysqli_query($con, $qry);
                    $j++;
                }
            }else{
            	//если родитель есть в бд, обновляем данные при их несовпадении
                while($param = current($params_parent)){
                    if($parents[$i][$param] != $stud_db['parents'][$i][$param] ){
                        $qry = "update kod.parent set ".key($params_parent)." = '".$parents[$i][$param]."' where id_parent = ".$parents[$i]['id_parent'].";";
                    }
                    next($params_parent);
                }
            }
        }
    }

	//изменение группы при несовпалении id в связи студента и группы
    if($group_id_db != $group_id){
        global $con;
        mysqli_query($con,"update kod.group_stud set gs_group_id = $group_id where gs_stud_id = ".$stud['id_stud'].";");
        
        //удаление старых записей успеваемости
        mysqli_query($con, "delete from kod.stud_performance where sp_id_stud".$stud['id_stud'].";");
        
        //создание новых записей успеваемости
        addPoints($stud['id_stud'],$group_num);
    }

    //обновление записей адресов при их несовпадении
    for($i=0; $i<2; $i++){
        $check = $address[$i]['type_address'];
        $j=0;
        foreach($params_address as $param){
            if($address_db[$check][$param] != $address[$i][$param]){
                if($param == 'name_country'){
                    $county_id = getCounryId($address[$i][$param]);
                    if(is_null($county_id)){
                        $county_id = getCounryId($address[$i][$param]);
                    }
                    $address[$i][$param] = $county_id;
                }
                $qry = "update kod.address set ".array_keys($params_address)[$j]." = '".$address[$i][$param]."' where id_address = ".$address[$i]['id_address'].";";
                mysqli_query($con, $qry);
            }
            $j++;
        }
    }

    //функция для создания новых записей успеваемости при изменении группы
    function addPoints($id_stud, $group_num){
        global $con;

        $cur_qry = "SELECT * FROM kod.curriculum where cur_group_num like '".$group_num."';";
        $cur_result = mysqli_query($con, $cur_qry);
        if(mysqli_num_rows($cur_result) > 0) {
            while ($row_cur = mysqli_fetch_assoc($cur_result)) {
                $id_cur = $row_cur['id_cur'];

                mysqli_query($con, "INSERT INTO kod.stud_performance (sp_id_stud, sp_id_cur, points)
    										VALUES (".$id_stud.", ".$id_cur.", '0');");
            }
        }
    }

    //функция для ссоздания/получения id страны
    function getCounryId($country_name){
        global $con;
	
		//запрос для поиска id страны
        $country_qry = mysqli_query($con, "select id_country from kod.country where name_country like '$country_name'");
        if(mysqli_num_rows($country_qry) > 0) {
            $row_country = mysqli_fetch_assoc($country_qry);
            return $row_country['id_country'];
        }
        else{
			//запрос для добавления страны, если такой не было в бд
            mysqli_query($con, "insert into kod.country (name_country) values ('".$country_name."')");
            return null;
        }
    }
    
    echo "Данные внесены успешно";