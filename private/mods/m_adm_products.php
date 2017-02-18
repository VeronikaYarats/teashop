<?php
// Мод обслуживающий редактирование продуктов

function m_adm_products($argv = array())
{
	global $global_marks;
    $tpl = new strontium_tpl("private/tpl/m_adm_products.html",
                             $global_marks, false); 
                             
    $mode = 'list_products';
    if(isset($argv['mode']))
        $mode = $argv['mode'];
    switch($mode) {
    	case "list_products":
    		if(!isset($argv['cat_id']))
    		  $cat_id = 1;
    		else 
    		$cat_id = $argv["cat_id"];
    		
    		if($cat_id)
    		/* вывод меню выбора категории */
    		$tpl->assign("category_menu");
            $categories = product_categories_get_list();
            /* вывод списка категорий */
            foreach($categories as $category) {
            	$cat = $category;
            	
            	/* вывод названия категории */
            	if($category['id'] == $cat_id) { 
                	$cat_name = $category['category_name'];
                	$cat['selected'] = 'selected';
            	}
            	$tpl->assign("categories_list", $cat);
            }
            /* вывод названия выбранной категории */
            $tpl->assign("category_name", array('category_name' => $cat_name));
            
            /* вывод списка продуктов выбранной категории */
            $tpl->assign("products_list", array('cat_id' => $cat_id));
            $products = products_get_list_by_category($cat_id);
            foreach($products as $product)
              $tpl->assign("products_row_table", $product);
            return $tpl->result();
    		break;
    	
    	case "edit_product":
    		$id = $argv['id'];
    		/* Вывод статических свойств */
    		$product = product_get_by_id($id);
    		if($product[0]['public'] == 1)
    			$product[0]['public'] = "checked";
    		$tpl->assign("product_add_edit", $product[0]);
    		$tpl->assign("product_edit",array("id" => $id));
    		$tpl->assign("product_query_edit");
    		/* Вывод динамических свойства */
    		$dinamic_properties = product_get_dynamic_properties($id);
    		foreach($dinamic_properties as $property) {
    		  	$tpl->assign("dinamic_property", $property);
    		  	$variants = product_get_variants_by_property($property['id']);
    		  	foreach ($variants as $variant) {
    		      if ($property['value'] == $variant['variant'])
    		          $variant['selected'] = "selected";
    		      $tpl->assign("variants_value_property_list", $variant);
    		  }
    		}
    		/* Вывод изображения */
    		$image_sizes = array('big' => array('w' => 0), 
							'mini' => array('w' => 150));
    		$image = get_first_object_image('products', $id, $image_sizes, $order = 'ASC');
    		if($image)
    			$tpl->assign("image_prev", $image);
    		return $tpl->result();
    		break;
       
        case "add_product":
        	$cat_id = $_GET['cat_id'];
        	$tpl->assign("product_add_edit",  array('cat_id' => $cat_id));
        	$tpl->assign("product_add");
        	$tpl->assign("product_query_add");
        	/* Вывод возможных динамических свойства */
        	$dinamic_properties = product_category_get_dynamic_properties($cat_id);
        	foreach($dinamic_properties as $id => $property) {
        	   $property['id'] = $id;
               $tpl->assign("dinamic_property", $property);
        	   $variants = product_get_variants_by_property($id);
               foreach ($variants as $variant) 
                  $tpl->assign("variants_value_property_list", $variant);
            }
        	return $tpl->result();
            break;
        	
    }
}


?>