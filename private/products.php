<?php
/* Функции для работа с продуктами */

/**
 * Возаращает название, страну, вес, цену продукта по категории
 * @param $id - категории продукта
 */
function products_get_list_by_category($cat_id)
{
	$query = "SELECT id, name_product, country, weight, price FROM products 
             WHERE product_category_id = ". $cat_id;
    return db_query($query);
	
}
/**
 * Получает названия и занчения динамических свойств продукта
 * @param $product_id - id продукта свойства
 * Возвращает массив['название свойства' => 'значение']
 */
function  product_get_dynamic_properties($product_id)
{
	$query = "SELECT product_category_id FROM products WHERE id = " . $product_id;
    $cat_id = db_query($query); //категория продукта для свойства
    if ($cat_id < 0)
        return ESQL;
         
    $cat_id = $cat_id[0]['product_category_id'];
	$query = "SELECT id, name, type FROM product_properties 
	          WHERE product_category_id = " . $cat_id;
	
	$product_properties = db_query($query); //для каждого продукта получили список динамических свойств
	if ($product_properties < 0)
        return ESQL;
	foreach($product_properties as $product_property) { 
	   if($product_property['type'] = "enum") { //для каждого свойства продукта получили значение
	       $query = "SELECT variant FROM product_property_enum WHERE property_id = " . 
	                  $product_property['id'] . " and id = 
	                  (SELECT value FROM product_properties_values WHERE product_id = ". 
	                  $product_id . " and property_id = " . $product_property['id'] . ')';
	                 
           $value = db_query($query); // получили значение свойства
           if ($value < 0)
                return ESQL;
           
           $array[$product_property['name']] = $value[0]['variant'];
        }
	}
	return $array;
}


?>