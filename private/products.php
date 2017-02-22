<?php
/* Функции для работа с продуктами */

$products = "products";
$product_properties_values = 'product_properties_values';


/**
 * возвращает значения таблицы products
 * @param $id - id продукта
 * @return массив - если запрос успешно
 *         ESQL - если запрос не выполнен
 */
function product_get_by_id($id)
{
	$query = "SELECT * FROM products WHERE id = " . $id;
    return db_query($query);
}

/**
 * Возвращает id изображения продукта
 * @param $id
 */
function get_img_id_by_product_id($id)
{
	$query = "SELECT img_id FROM object_images WHERE obj_id = " . $id . " AND obj_type LIKE 'products'";
	return db_query($query);
}

/**
 * возвращает категорию
 * @param $cat_id
 */
function get_category_by_cat_id($cat_id)
{
	$query = "SELECT * FROM product_category WHERE id = " . $cat_id;
	$result = db_query($query);
	return $result[0];
}


/**
 * Возвращает список всех категорий
 */
function product_categories_get_list()
{
	$query = "SELECT * FROM product_category";
	return db_query($query);
}


/**
 * Возвращает список продуктов категории
 * @param $cat_id - id категории продукта
 * @return массив - если запрос успешно
 *         ESQL - если запрос не выполнен
 */
function products_get_list_by_category($cat_id)
{
    $cat_id = (int)$cat_id;
    $query = "SELECT * FROM products 
             WHERE product_category_id = ". $cat_id;
    return db_query($query);
}


/**
 * Получает список динамических свойст категории
 * @param $cat_id - id категории продукта
 * @return массив в формате id свойства => array('название свойства', 'тип свойства')
 */
function product_category_get_dynamic_properties($cat_id)
{
    $cat_id = (int)$cat_id;
	$query = "SELECT id, name, type FROM product_properties 
              WHERE product_category_id = " . $cat_id;
    $category_properties = db_query($query);
    foreach($category_properties as $category_property) 
        $properties[$category_property['id']] = array('name' => $category_property['name'],
                                                      'type' => $category_property['type']);
    return $properties;
}


/**
 * Получает список вариантов значений свойства
 * @param $property_id - id свойства
 * @return массив - если запрос успешно
 *         ESQL - если запрос не выполнен
 */
function product_get_variants_by_property($property_id)
{
	$query = "SELECT * FROM product_property_enum WHERE property_id = " .$property_id;
	return db_query($query);
}


/**
 *  Получаем значение динамического свойства продукта product_properties_values
 * @param $product_id
 * @param $property_id
 */
function get_dinamic_value($product_id, $property_id)
{
	$query = "SELECT value FROM product_properties_values " .
                          		"WHERE product_id = ". $product_id . " " .
                          		"AND property_id = " . $property_id;
	return db_query($query);
}



/**
 * Получает список динамических свойств продукта
 * @param $product_id - id продукта
 * @return массив в формате id свойства => array('название свойства', 'значение свойства')
 *         или ошибка
 */
function product_get_dynamic_properties ($product_id)
{
	$query = "SELECT * FROM product_properties_values 
			WHERE product_id = " . $product_id;
	$product_properties_values = db_query($query);
	if ($product_properties_values < 0 || !$product_properties_values)
		return $product_properties_values;
		
	foreach($product_properties_values as $product_properties_value) {
		$property_id = $product_properties_value['property_id'];
		$query ="SELECT * FROM product_properties WHERE id = " . $property_id;
		$product_properties = db_query($query);	
		foreach($product_properties as $product_property) {
			$property_name = $product_property['name']; //название свойства
			
			/* анализ типа данных свойства */
			switch($product_property['type']) {
				/* тип данных ENUM */
				case 'enum':
					$query = "SELECT * FROM product_property_enum WHERE id = " . $product_properties_value['value'];
					$variant = db_query($query);
					$property_value = $variant[0]['variant'];//название варианта
					$properties[$property_id] = array("name" => $property_name,
                                     			  	  "value" => $property_value);	
					break;
				/* тип данных STRING */	
				case 'string':
				/* тип данных INTEGER */	
				case 'integer':
					/* получение значение свойства */
            		$row = get_dinamic_value($product_id, $property_id);
            		if ($row < 0)
              			return $row;
            		$value = $row[0]['value'];
            		$properties[$property_id] = array("name" => $property_name,
                                                         "value" => $value);
           			break;
			}
		}
	}
	return($properties);
}



/**
 * Редактирует статические свойства в таблице products
 * @param $product_id - id продукта
 * @param $argv - массив данных для обновления
 * @return ESQL - в случае неудачи
 *         0 - в случае удачного обновления
 */
function products_edit($product_id, $arg_list)
{
	global $products;
	$fields = array('id', 'name', 'country', 'weight', 'price', 'public', 'trade_mark', 'description');
	foreach ($arg_list as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
   return db_update($products, $product_id, $data);
}


/**
 * Редактирует динамические свойства в таблице product_properties_vdlues
 * @param $product_id - id продукта
 * @param $argv - массив данных для обновления
 * @return ESQL - в случае неудачи
 *         0 - в случае удачного обновления
 */
function edit_dinamic_property($product_id, $arg_list)
{
	global $product_properties_values;
	global $link;
	
	$property_id = $arg_list['property_id'];
	$value = $arg_list['value'];
	
	$query = "DELETE FROM " . $product_properties_values .  " " .
              "WHERE product_id = " . $product_id . " AND property_id = " . $property_id;
	$result = db_query($query);
	if(!$result)
	   return ESQL;
	return product_add_dinamic_property($product_id, $property_id, $value);
}


/**
 * Добавление нового продукта в таблицу products
 * @param $argv
 * @return id - продукта
 *         ESQL - в случае неудачи
 */
function product_add_static_properties($arg_list)
{
    global $products;
    $fields = array('name', 'country', 'weight', 'price', 'public', 'product_category_id', 'trade_mark', 'description');
    foreach ($arg_list as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;        
    return db_insert($products, $data);
}

/**
 * 
 * Добавление динамических свойст продуткта
 * @param $product_id - id продукта
 * @param $property_id - id свойста
 * @param $value - id свойства
 * @return 0 - в случает успеха
 *         ESQL - в случае неудачи
 */
function product_add_dinamic_property($product_id, $property_id, $value)
{
    global $product_properties_values;
    
    $query = "INSERT INTO " . $product_properties_values . " SET value = " .$value . 
              ", property_id = " .  $property_id . ", product_id = " . $product_id;
              "WHERE product_id = " . $product_id . " AND property_id = " . $property_id;
    $result = db_query($query);
    if($result)
       return 0;
    else 
       return ESQL;	
}

/**
 * Удаление продукта из products и product_properties_values
 * @param $product_id - $id продукта
 * @return  - 1 в случае успеха
 *            ESQL - если запрос не выполнен
 */
function product_del($product_id)
{
	global $products;
	
	$query = "DELETE FROM " . $products . " WHERE id = " .$product_id;
	$result = db_query($query);
	if(!result)
	   return result;
	   
	return del_dinamic_prop($product_id);
}

function del_dinamic_prop($product_id)
{	
	global $product_properties_values;
	$query = "DELETE FROM " . $product_properties_values .  " " .
              "WHERE product_id = " . $product_id;
	return db_query($query);
}
?>