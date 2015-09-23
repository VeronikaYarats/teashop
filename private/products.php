<?php
/* Функции для работа с продуктами */

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
 * Получает список динамических свойств продукта
 * @param $product_id - id продукта
 * @return массив в формате ('название свойства' => 'значение свойства')
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
    
    /* получение динамических свойств для продукта $product_id */
    $query = "SELECT id, name, type FROM product_properties 
              WHERE product_category_id = " . $cat_id;
    $product_properties = db_query($query); 
    if ($product_properties < 0)
        return $product_properties;

    $properties = array();
    foreach($product_properties as $product_property) { 

        /* анализ типа данных свойства */
        switch ($product_property['type']) { 
        /* тип данных ENUM */
        case 'enum':
            /* получение значение свойства */
            $query = "SELECT variant FROM product_property_enum " .
                     "WHERE property_id = " . $product_property['id'] . " " .
                     "AND id = (" .
                          "SELECT value FROM product_properties_values " .
                          "WHERE product_id = ". $product_id . " " .
                          "AND property_id = " . $product_property['id'] . 
                               ')';

            $rows = db_query($query);
            if ($rows < 0)
              return $rows;
            $value = $rows[0]['variant'];

            $properties[$product_property['name']] = $value;
            break;
            
        /* тип данных STRING */
        case 'string':
        	/* получение значение свойства */
        	$query = "SELECT value FROM product_properties_values " .
                          "WHERE product_id = ". $product_id . " " .
                          "AND property_id = " . $product_property['id'];
        	$rows = db_query($query);
        	if ($rows < 0)
              return $rows;
            $value = $rows[0]['value'];
            $properties[$product_property['name']] = $value;
        	break;

        /* тип данных INTEGER */
        case 'integer':
            $query = "SELECT value FROM product_properties_values " .
                          "WHERE product_id = ". $product_id . " " .
                          "AND property_id = " . $product_property['id'];
            $rows = db_query($query);
            if ($rows < 0)
              return $rows;
            $value = $rows[0]['value'];
            $properties[$product_property['name']] = $value;
            break;
        }
    }
    return $properties;
}


?>