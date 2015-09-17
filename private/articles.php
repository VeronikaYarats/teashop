<?php

// функции для работы со статьями

require_once("common/base_sql.php"); //файл для работы с базой даных
$table_name = "articles";



/**
 * Получить ассоциативный массив с данными записи с идентификатором $id
 * @param $id идентификатор записи
 * @return EINVAL в случае ошибки входных параметров
 * @return массив [индентификатор, имя страницы, имя, содержание, публикование]
 */
function article_get_by_id($id)
{
    global $table_name;
    
	if (!is_numeric($id) || !isset($id)) {
		dbg_err("Incorrect id"); 
        return EINVAL;
	}
    
    $query = "SELECT * FROM ". $table_name .  " WHERE id = " . $id;
    $result = db_query($query);
    
    if ($result == FALSE) { // если бд вернула 0 строк
        dbg_err("Article not found"); 
        return EINVAL;
    }
    return $result[0];
}	




/**
 * добавляет статью в бд
 * @param $array_params - массив с данными
 * @param [page_title] - title страницы
 * @param [name] - название стать
 * @param [contents] - содержимое статьи
 * @param [public] - публикация статьи
 * @return EINVAL в случае ошибки входных параметров
 * @return EBASE в случае ошибки связи с базой
 * @return id в случае успешного добавления
 */
function article_add_new($array_params)
{
    global $table_name;
    
    if(!isset($array_params["page_title"])) { 
        dbg_err("Not set page title");    
        return EINVAL;    
    }
    
    if(!isset($array_params["name"])) {
	    dbg_err("Not set name");    
	    return EINVAL;
    }
    
    if(!isset($array_params["contents"])) {
	    dbg_err("Not set contents");    
	    return EINVAL;    
    }
    
    if(!isset($array_params["public"])) {
        dbg_err("Not set public");    
        return EINVAL;    
    }
    
    return(db_insert($table_name, $array_params));
}

?>