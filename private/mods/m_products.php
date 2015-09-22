<?php 
/*  код обслуживающий продукты */

function m_products($argv=array())
{
	$tpl = new strontium_tpl("private/tpl/m_products.html", array(), false);
	if(isset($argv['cat_id']))
	   $id = $argv['cat_id'];
	
	$products = get_product_by_category($id);
	
	if($products < 0) //если запрос не нашел продукты
	   $tpl->assign("product_error_message");
	else {
	foreach($products as $product)
	   $tpl->assign("products", $product);
	}
	return $tpl->result();
}

?>