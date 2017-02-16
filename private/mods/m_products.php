<?php 
/*  код обслуживающий продукты */

function m_products($argv=array())
{
	$products_count = 0;
    $tpl = new strontium_tpl("private/tpl/m_products.html", $global_marks, false);
    $tpl->assign(product_search);
    if(isset($argv['cat_id']))
       $cat_id = $argv['cat_id'];
    
    $products = products_get_list_by_category($cat_id);
    foreach($products as $product) {
       if (!$product['public'])
          continue;  
		
       $tpl->assign("products", $product);
       $product_id = $product['id'];
       $properties = product_get_dynamic_properties($product_id);
       if($properties < 0)
           continue;
       else 
	       foreach ($properties as $property)
		       $tpl->assign('dymnamic_property',$property);
		$products_count++;   
		$image_sizes = array('big' => array('w' => 0), 
							'mini' => array('w' => 150));
		$images = get_first_object_image('products', $product_id, $image_sizes);
		$tpl->assign('image',$images);
	    
    }
    if($products_count < 1)
    	$tpl->assign("product_error_message"); 	
    return $tpl->result();
    
}

?>