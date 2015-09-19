<?php
require_once "private/articles.php";
require_once "private/common/message_box.php";

session_start();

if(isset($_POST['post_query']))
    switch ($_POST['post_query']) {
        case "article_edit":
            $id = $_POST['article_id'];
            if ($_POST['public'])
              $public = 1;
            else 
              $public = 0;
            $data = array("page_title" => $_POST['page_title'], "name" => $_POST['name'],
                          "contents" => $_POST['contents'], "public" => $public);
            if(article_edit($id, $data) == 0) {
                $block = "message_article_success_edit";
                $data = array('article_id' => $id);
                display_message_box($block, $data);
            }
            header( 'Location: index.php');
            break;
            
        case "article_add":
            if ($_POST['public'])
              $public = 1;
            else 
              $public = 0;
            
            $data = array("page_title" => $_POST['page_title'], "name" => $_POST['name'],
                          "contents" => $_POST['contents'], "public" => $public);
            article_add_new($data);
            header( 'Location: ' . HTTP_ROOT_PATH . 'index.php');
            break;
    }

if(isset($_GET['get_query']))    
    switch ($_GET['get_query']) {
        case "del_article":
            $id = $_GET['article_id'];
            article_del($id);
            header( 'Location: index.php');
            break;
    }
?>