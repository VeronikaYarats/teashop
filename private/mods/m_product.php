<?php
/* модуль вывода информации о продукте */
function m_product($arg_list)
{
	$tpl = new strontium_tpl("private/tpl/m_product.html",
                             $global_marks, false); 
	$product_id = $arg_list['id'];
	$product = product_get_by_id($product_id);
	$tpl->assign("product_description", $product[0]);
	
	$dinamic_properties = product_get_dynamic_properties ($product_id);
   	foreach($dinamic_properties as $dinamic_property)
   		$tpl->assign("dinamic_property", $dinamic_property);
   		
   	$image_sizes = array('big' => array('w' => 0), 
						 'mini' => array('w' => 300));
    $image = get_first_object_image('products', $product_id, 
    								$image_sizes, $order = 'ASC');
   	if($image)
    	$tpl->assign("image", $image);
	return $tpl->result();
}

?>