<?php

/**
 * Открывает соединение с базой данных
 * @return 0 - в случае ошибки 
 */

global $link;
function db_init()
{
	global $link;
	$host = '127.0.0.1	';
	$user = 'admin';
	$pass = '13941';
	$database = 'veroshop';
	$port = 3306;
	return $link = mysqli_connect($host, $user, $pass, $database, $port);
}
db_init();



/**
 * Выполняет запрос
 *@param $query - запрос
 *@return 
 *@return 1 - если запрос успешно
 *@return 2 - если запрос не выполнен
 *@return data - возвращает ассоциативный массив как результат запроса
 */
function db_query($query)
{
	$row = array();
	global $link;
	$data = array();
	
	$result = mysqli_query($link, $query);
	if($result === TRUE)
		return 1;
	if($result === FALSE)
		return 2;
		
		
	while($row = mysqli_fetch_assoc($result))
		$data[] = $row;
	return $data;
}


/** 
 * Добавляет запись в БД
 * @param $table_name - имя таблицы для добавления
 * @param $array - массив данных для добавления
 * @return 0 -возвращает 0 в случае неудачи
 * @return $id - возвращает id вставленной записи
 */
function db_insert($table_name, $array)
{
	
	global $link;
	$query = "INSERT INTO " . $table_name . " SET ";
	$separator = '';
	foreach ($array as $field => $value) {
		if($field == 'id')
			continue;
		$query .= $separator . $field . ' = "' . $value . '"';
		$separator = ',';
	}
	
	$result = mysqli_query($link, $query);
	if($result === FALSE)
		return EBASE;
	else
		return mysqli_insert_id($link);
}



/**
 * Обновляет данные в БД с указанным id
 * @param $table - имя таблицы для обновления
 * @param $id - id записи для обновления
 * @param $array - массив данных для обновления
 * @return 0 - в случае неудачи
 * @return 1 - в случае удачного обновления
 */
function db_update($table, $id, $array)
{
	global $link;
	$separator = '';
	$query = "UPDATE " . $table . " SET "; 
	foreach($array as $field	 => $value) {
		$query .= $separator . $field . ' = "' . $value . '"';
		$separator = ',';
	}
	$query .= " WHERE id = " . $id;
	return mysqli_query($link, $query);
}


/**
 * Закрывает ранее открытое соединение с базой данных
 * @return 0 - в случае ошибки
 */
function db_close()
{
	global $link;
	return mysqli_close($link);	
}

?>