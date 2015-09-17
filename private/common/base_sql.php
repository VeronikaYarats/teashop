<?php

/**
 * ��������� ���������� � ����� ������
 * @return 0 - � ������ ������ 
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
 * ��������� ������
 *@param $query - ������
 *@return 
 *@return 1 - ���� ������ �������
 *@return 2 - ���� ������ �� ��������
 *@return data - ���������� ������������� ������ ��� ��������� �������
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
 * ��������� ������ � ��
 * @param $table_name - ��� ������� ��� ����������
 * @param $array - ������ ������ ��� ����������
 * @return 0 -���������� 0 � ������ �������
 * @return $id - ���������� id ����������� ������
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
 * ��������� ������ � �� � ��������� id
 * @param $table - ��� ������� ��� ����������
 * @param $id - id ������ ��� ����������
 * @param $array - ������ ������ ��� ����������
 * @return 0 - � ������ �������
 * @return 1 - � ������ �������� ����������
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
 * ��������� ����� �������� ���������� � ����� ������
 * @return 0 - � ������ ������
 */
function db_close()
{
	global $link;
	return mysqli_close($link);	
}

?>