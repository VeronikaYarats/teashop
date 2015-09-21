<?php
/* ������� ������� ��� ������ � �� */

/**
 * ��������� ������
 *@param $query - ������
 *@return 1 - ���� ������ �������
 *        EBASE - ���� ������ �� ��������
 *        data - ���������� ������������� ������ ��� ��������� �������
 */
function db_query($query)
{
	global $link;
	$data = array();
	$row = array();
	
	$result = mysqli_query($link, $query);
	if($result === TRUE)
		return 1;
	if($result === FALSE)
		return EBASE;
		
	while($row = mysqli_fetch_assoc($result))
		$data[] = $row;
	return $data;
}


/** 
 * ��������� ������ � ��
 * @param $table_name - ��� ������� ��� ����������
 * @param $array - ������ ������ ��� ����������
 * @return EBASE - � ������ �������
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
 * @return EBASE - � ������ �������
 *         0 - � ������ �������� ����������
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
	$update = mysqli_query($link, $query);
	if($update)
	   return 0;
	else 
	   return EBASE;
	
}


/**
 * ��������� ����� �������� ���������� � ����� ������
 * @return EBASE - � ������ ������
 * @return 1 - � ������ ������
 */
function db_close()
{
	global $link;
	if(!mysqli_close($link))
	   return EBASE;
	else 
	return 1;	
}

?>