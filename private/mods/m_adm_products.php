<?php
// Мод обслуживающий редактирование продуктов

function m_adm_products($arg_list)
{
    global $global_marks;
    $tpl = new strontium_tpl("private/tpl/m_adm_products.html",
                             global_conf()['global_marks'], false);
     
    $mode = 'list_products';
    if(isset($arg_list['mode']))
        $mode = $arg_list['mode'];
    switch($mode) {
    case "list_products":
        page_set_title("продукты");
        if(!isset($arg_list['cat_id']))
            $cat_id = 1;
        else
            $cat_id = $arg_list["cat_id"];

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
            $cat['url'] = mk_url(array('mod' => 'adm_products', 
                                        'mode' => 'list_products', 
                                        'cat_id' => $cat['id']));
            $tpl->assign("categories_list", $cat);
        }
        /* вывод названия выбранной категории */
        $tpl->assign("category_name", array('category_name' => $cat_name));

        /* вывод списка продуктов выбранной категории */
       
        $url_add = mk_url(array('mod' => 'adm_products',
                                    'mode' => 'add_product',
                                    'cat_id' => $cat_id
                                    ));
        $tpl->assign("products_list", array('cat_id' => $cat_id, 'add_url' => $url_add));
        $products = products_get_list_by_category($cat_id);
            
        foreach($products as $product){
            $url_edit = mk_url(array('mod' => 'adm_products',
                                'mode' => 'edit_product',
                                'key' => $product['key'],
                                ));
            $url_del = mk_url(array('mod' => 'adm_products',
                                'get_query' => 'del_product',
                                'id' => $product['id']) );

            $product['edit_url'] = $url_edit;
            $product['delete_url'] = $url_del;
            $tpl->assign("products_row_table", $product);
        }
        return $tpl->result();
        break;
         
    case "edit_product":
        page_set_title("редактирование продукта");
        $product_key = $arg_list['key'];
        //$cat_id = $arg_list['cat_id'];
        /* Вывод статических свойств */
        $product = product_get_by_key($product_key);
        if($product[0]['public'] == 1)
            $product[0]['public'] = "checked";
        $product_id = $product[0]['id'];
        $cat_id = $product[0]['product_category_id'];
        $tpl->assign("product_add_edit", $product[0]);
        $tpl->assign("product_edit",array("id" => $product_id));
        $tpl->assign("product_query_edit");
        /* Вывод динамических свойства */
        $dinamic_properties = product_category_get_dynamic_properties($cat_id);
        foreach($dinamic_properties as $id => $property) {
            $property['id'] = $id;
            /* Вывод динамических свойств */
            $tpl->assign("dinamic_property", $property);
            /* Если тип значения свойства enum */
            if($property['type'] == 'enum') {
                /* Получаем варианты перечесления */
                $variants = product_get_variants_by_property($id);
                foreach ($variants as $variant) {
                    $value = get_dinamic_value($product_id, $property['id']);
                    if ($variant['id'] == $value[0]['value'])
                        $variant['selected'] = "selected";
                    $tpl->assign("variants_value_property_list", $variant);
                }
            }
        }
        /* Вывод изображения */
        $image_sizes = array('big' => array('w' => 0),
						'mini' => array('w' => 150));
        $image = get_first_object_image('products', $product_id,
        $image_sizes, $order = 'ASC');
        if($image)
            $tpl->assign("image_prev", $image);
        return $tpl->result();
        break;
         
    case "add_product":
        page_set_title("добавление продукта");
        $cat_id = $arg_list['cat_id'];
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