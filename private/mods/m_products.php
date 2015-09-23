<?php 
/*  код обслуживающий продукты */

function m_products($argv=array())
{
    $tpl = new strontium_tpl("private/tpl/m_products.html", array(), false);
    if(isset($argv['cat_id']))
       $cat_id = $argv['cat_id'];
    
    $products = products_get_list_by_category($cat_id);
    if($products < 0) //если запрос не нашел продукты
       $tpl->assign("product_error_message");
    else {
    foreach($products as $product) {
       $tpl->assign("products", $product);
       $product_id = $product['id'];
       $properties = product_get_dynamic_properties($product_id);
       if($properties < 0)
           continue;
       else 
	       foreach ($properties as $property_name => $property_value)
		       $tpl->assign('dymnamic_property', 
		                    array('name' => $property_name, 'value' => $property_value));
		       
    }   
    }
    return $tpl->result();
}

?>