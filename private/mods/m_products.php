<?php
/*  код обслуживающий продукты */

function m_products($arg_list)
{
    $products_count = 0;
    $tpl = new strontium_tpl("private/tpl/m_products.html", 
                            array(), false);
    $tpl->assign(product_search);
    if(isset($arg_list['cat_id']))
        $cat_id = $arg_list['cat_id'];
    $category = get_category_by_cat_id($cat_id);
    page_set_title($category['page_title']);
    $products = products_get_list_by_category($cat_id);
    foreach($products as $product) {
        if (!$product['public'])
            continue;

        $tpl->assign("products", $product);
       	$product_id = $product['id'];
       	$properties = product_get_dynamic_properties($product_id);
       	if($properties < 0)
       	    continue;
        
       	foreach($properties as $property)
       	    $tpl->assign('dymnamic_property',$property);
       	$products_count++;
       	$image_sizes = array('big' => array('w' => 0),
       	                    'mini' => array('w' => 100));
       	$image = get_first_object_image('products', $product_id, $image_sizes);
       	
       	$url_params = array('mod' => 'product', 'cat_id' => $cat_id, 'key' => $product['key']);
       	$url = mk_url($url_params);
       	if($image) {
       	    $image['url'] = $url;
       	    $image['product_id'] = $product_id;
       	    $tpl->assign('image',$image);
       	}
       	 
    }
    if($products_count < 1)
        $tpl->assign("product_error_message");
    return $tpl->result();
}