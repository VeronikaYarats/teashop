<?php
/* Универсальный обработчик входящих запросов от различных форм */

require_once "private/common/debug.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/common/images.php";
require_once "private/articles.php";
require_once "private/products.php";
require_once "private/init.php";
require_once "private/users.php";

session_start();

/* Обработчик POST запросов */
if(isset($_POST['post_query']))
    switch ($_POST['post_query']) {

        /* Редактирование статьи */
        case "article_edit":
            $id = $_POST['article_id'];
            if(!auth_get_admin())
            	continue;
            $public = isset($_POST['public']);

            $array = $_POST;
            $array["public"] = $public;
            $err = article_edit($id, $array);
            switch ($err) {
            	case 0:
            		$block = "message_article_success_edit";
                    $data = array('article_id' => $id);
                    break;
                    
            	case EINVAL:
            		$block = "message_einval";
            		break;
            		
            	case ESQL:
            		$block = "message_esql";
            		break;
            }
            message_box_display($block, $data);
           	header('Location: index.php?mod=adm_articles');
            break;
            
        /* Добавление новой статьи */
        case "article_add":
        	if(!auth_get_admin())
                continue;
            $data = $_POST;
            $public = isset($_POST['public']);
            $data["public"] = $public;
            $article_id = article_add_new($data);
            switch ($article_id) {
            	case EINVAL:
                    $block = "message_einval";
                    break;
                    
                case ESQL:
                    $block = "message_esql";
                    break;

                default:
                    $block = "message_article_success_add_new";
                    $data = array('article_id' => $article_id);
                    break;
            }
            message_box_display($block, $data);
            header('Location: index.php?mod=adm_articles');
            break;

        /* Редактирование продукта */
        case "edit_product":
        	if(!auth_get_admin())
            	continue;
        	$id = $_POST['product_id'];
        	$cat_id = $_POST['cat_id'];
            if(!auth_get_admin())
            	continue;
            $public = isset($_POST['public']);
            $array = $_POST;
            $array["public"] = $public;
            /* Редактирование статический свойств */
            $err = products_edit($id, $array);
            if ($err < 0) 
            	$block = "message_esql";
            
             /* Редактирование динамических свойств */
            $dinamic_properties = $_POST['dinamic_property'];
            foreach($dinamic_properties as $dinamic_property) {
            	if($dinamic_property['value'] == 'none')
            		del_dinamic_prop($product_id);
                $err = edit_dinamic_property($id, $dinamic_property);  
                if ($err < 0) {
                    $block = "message_esql";
                    break;
                }
                else 
                    $block = "message_product_success_edit";
            }
            
            /* Редактирование изображения */
			$title = $array['trade_mark'] . ' ' . $array['name'];
         	$alt = $title;
         	$image_url = $_POST['image_url'];
         	/* Удаление изображение если выбран checkbox delete_image
         	 * или загружено новое изображение */
         	if(($_POST['img_id'] && ($image_url || !$_FILES['image']['error'])) || 
         		$_POST['delete_image']) {
         		$err = del_image_from_object('products', $id, $_POST['img_id']);
         		if($err < 0)
         			$block = "message_image_add_error";	
         		}
   			/* Добавление изображения через поле File */	
 			if(!$_FILES['image']['error']) 
 				$img_id = upload_image('image', '', $alt, $title);
 			
 			/* Добавление изображения через поле URL */
 			if($image_url) 
 				$img_id = download_image($image_url, '', $alt, $title);
         
 			if(!is_NULL($img_id)) {
 				if($img_id > 0)
					add_image_to_object('products', $id, $img_id);
				else
           	 		$block = "message_image_add_error";
 			}
 			
 			message_box_display($block, array('product_id' => $id));
          	header('Location: index.php?mod=adm_products&mode=list_products&cat_id='. $cat_id);
            break;
            
        /* Добавление продукта */
        case "add_product":
            $data = $_POST;
            if(!auth_get_admin())
                continue;
         	$public = isset($_POST['public']);
            $data["public"] = $public;
            /* добавляем продукт в талицу products */
         	$product_id = product_add_static_properties($data);
         	if ($product_id < 0) {
                $block = "message_esql";
                break;
         	}
         	else
         		 $block = "message_product_success_add";
         
         	/* добавляем динамические свойства */
         	$dinamic_properties = $_POST['dinamic_property'];
         	
         	foreach ($dinamic_properties as $dinamic_property) {
         		if($dinamic_property['value'] == 'none')
            		continue;
            			
                $err = product_add_dinamic_property($product_id, $dinamic_property['property_id'], 
         	                                 $dinamic_property['value']);
         	    if($err < 0) {
         	      $block = "message_esql";
         	      break;
         	    }
         	    else 
         	      $block = "message_product_success_add";
         	}
         	
         	/* Добавление изображение */
         	$title = $data['trade_mark'] . ' ' . $data['name'];
         	$alt = $title;

            $image_url = $_POST['image_url'];
           	if($image_url) 
           	 	$img_id = download_image($image_url, '', $alt, $title);

           	if(!$_FILES['image']['error']) 
            	$img_id = upload_image('image', '', $alt, $title);
     
    		if(!is_NULL($img_id)) {
 				if($img_id > 0)
					add_image_to_object('products', $id, $img_id);
				else
           	 		$block = "message_image_add_error";
 			}
 		
           	message_box_display($block, array('product_id' => $product_id));
         	header('Location: index.php?mod=adm_products&mode=list_products&cat_id='
         			. $_POST['product_category_id']);
         	break;

        /* Авторизация администратора сайта */
        case "adm_login":
        	if(($_POST['name'] == "veronika") && 
               ($_POST['password'] == "12345")) {
               	auth_store_admin(1);
                header( 'Location: index.php?mod=adm_articles');
            }
            else {
                message_box_display("message_adm_login_incorrect");
                header( 'Location: index.php?mod=adm_login');
            }
            break;

            /* Выбор списка продуктов по категории */
        case "get_category":
        	if(!isset($_POST["category_name"]))
        		$cat_id = 1;
        	else
        		$cat_id = $_POST["category_name"];
        		
        	header('Location: index.php?mod=adm_products&mode=list_products&cat_id='.$cat_id);
        	break;
   }
 
/* Обработчик GET запросов */
if(isset($_GET['get_query']))
    switch ($_GET['get_query']) {

        /* Удаление статьи */
        case "del_article":
        	if(!auth_get_admin())
                continue;
            $id = $_GET['article_id'];
            $err = article_del($id);
            switch ($err) {
            	case 0: 
            		$block = "message_article_success_del";
                    $data = array('article_id' => $id);
                    break;
                    
                case EINVAL:
                    $block = "message_einval";
                    break;
                    
                case ESQL:
                    $block = "message_esql";
                    break;                   
            }
            message_box_display($block, $data);
            header('Location: index.php?mod=adm_articles');
            break;

        /* Выход из режима администратора сайта */
        case "adm_logout":
        	auth_adm_remove();
        	header('Location: index.php');
        	break;
        	
        /* Удаление продукта */
        case "del_product":
            if(!auth_get_admin())
                continue;
            $product_id = $_GET['product_id'];
            $err = product_del($product_id);
            if(get_img_id_by_product_id($product_id))
          		del_images_object('products', $product_id);
            if ($err)
                $block = "message_product_success_del";  
            else 
                $block = "message_esql";
            message_box_display($block);
            header('Location: index.php?mod=adm_products');
            break;
    }