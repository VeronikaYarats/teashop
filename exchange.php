<?php
/* Универсальный обработчик входящих запросов от различных форм */

require_once "private/common/debug.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/articles.php";
require_once "private/init.php";

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
            		$block = "message_article_einval";
            		break;
            		
            	case EBASE:
            		$block = "message_article_ebase";
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
                    $block = "message_article_einval";
                    break;
                    
                case EBASE:
                    $block = "message_article_ebase";
                    break;

                default:
                    $block = "message_article_success_add_new";
                    $data = array('article_id' => $article_id);
                    break;
            }
            message_box_display($block, $data);
            header('Location: index.php?mod=adm_articles');
            break;
        
        /* Авторизация администратора сайта */
        case "adm_login":
        	if(($_POST['name'] == "veronika") && 
               ($_POST['password'] == "12345")) {
               	
         	    auth_store_admin(1);
                header( 'Location: index.php');
            }
            else {
                message_box_display("message_adm_login_incorrect");
                header( 'Location: index.php?mod=adm_login');
            }
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
                    $block = "message_article_einval";
                    break;
                    
                case EBASE:
                    $block = "message_article_ebase";
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
    }
?>