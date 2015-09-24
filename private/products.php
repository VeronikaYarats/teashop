<?php
/* Функции для работа с продуктами */
function product_get_by_id($id)
{
	$query = "SELECT * FROM products WHERE id = " . $id;
    return db_query($query);
}
	

/**
 * Возвращает список категорий
 */
function product_categories_get_list()
{
	$query = "SELECT * FROM product_category";
	return db_query($query);
}

/**
 * Возаращает список продуктов
 * @param $cat_id - id категории продукта
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
 * Получает список динамических свойств продукта
 * @param $product_id - id продукта
 * @return массив в формате id свойства => array('название свойства', 'значение свойства')
 *         или ошибка
 */
function product_get_dynamic_properties($product_id)
{
    $product_id = (int)$product_id;
    $query = "SELECT product_category_id FROM products WHERE id = " . $product_id;
    $rows = db_query($query); //категория продукта для свойства
    if ($rows < 0)
        return $rows;
         
    $cat_id = $rows[0]['product_category_id'];
    $product_properties = product_category_get_dynamic_properties($cat_id);
    if ($product_properties < 0)
        return $product_properties;
        
    foreach($product_properties as $id => $product_property)
        /* анализ типа данных свойства */
        switch ($product_property['type']) { 
        /* тип данных ENUM */
        case 'enum':
            /* получение значение свойства */
            $query = "SELECT variant FROM product_property_enum " .
                     "WHERE property_id = " . $id . " " .
                     "AND id = (" .
                          "SELECT value FROM product_properties_values " .
                          "WHERE product_id = ". $product_id . " " .
                          "AND property_id = " . $id . 
                               ')';
            $rows = db_query($query);
            if ($rows < 0)
              return $rows;
            $value = $rows[0]['variant'];
            $properties[$id] = array("name" => $product_property['name'],
                                                         "value" => $value);
            break;
            
        /* тип данных STRING */
        case 'string':
        /* тип данных INTEGER */
        case 'integer':
            /* получение значение свойства */
            $query = "SELECT value FROM product_properties_values " .
                          "WHERE product_id = ". $product_id . " " .
                          "AND property_id = " . $id;
            $rows = db_query($query);
            if ($rows < 0)
              return $rows;
            $value = $rows[0]['value'];
            $properties[$product_property['id']] = array("name" => $product_property['name'],
                                                         "value" => $value);
            break;
        }
  return $properties;
}


?>