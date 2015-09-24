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
    		
    		/* вывод меню выбора категории */
    		$tpl->assign("category_menu");
            $categories = product_categories_get_list();
            /* вывод списка категорий */
            foreach($categories as $category) {
            $tpl->assign("categories_list", $category);
            /* выбор названия категории */
            if($category['id'] == $cat_id) 
                $cat_name = $category['category_name'];
            }
            /* вывод названия выбранной категории */
            $tpl->assign("category_name", array('category_name' => $cat_name));
            
            /* вывод списка продуктов выбранной категории */
            $tpl->assign("products_list");
            $products = products_get_list_by_category($cat_id);
            foreach($products as $product)
              $tpl->assign("products_row_table", $product);
            return $tpl->result();
    		break;
    	
    	case "edit_product":
    		$tpl->assign("product_add_edit");
    		return $tpl->result();
    		break;
    }
}


?>