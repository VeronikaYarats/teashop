<?php
/* Функции для работа с продуктами */

/**
 * Возаращает название, страну, вес, цену продукта по категории
 * @param $id - категории продукта
 */
function get_product_by_category($id)
{
	$query = "SELECT name_product, country, weight, price FROM products 
	WHERE product_category_id = ". $id;
	return (db_query($query));
}

?>