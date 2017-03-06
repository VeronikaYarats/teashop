<?php
/* модуль вывода информации о продукте */
function m_product($arg_list)
{
    $tpl = new strontium_tpl("private/tpl/m_product.html",
                             array(), false);
 
    $product_key = $arg_list['key'];
    $product = product_get_by_key($product_key);
    if(!$product)
        $tpl->assign("product_not_found");  
    else {
        $title = $product[0]['trade_mark'] . ' ' . $product[0]['name'];
        page_set_title($title);
        $tpl->assign("product_description", $product[0]);
    
        $dinamic_properties = product_get_dynamic_properties ($product[0]['id']);
       	foreach($dinamic_properties as $dinamic_property)
       	    $tpl->assign("dinamic_property", $dinamic_property);
       	 
       	$image_sizes = array('big' => array('w' => 0),
       	                    'mini' => array('w' => 300));
        $image = get_first_object_image('products', $product[0]['id'],
        $image_sizes, $order = 'ASC');
       	if($image)
       	    $tpl->assign("image", $image);
    }
   	return $tpl->result();
}