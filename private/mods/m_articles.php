<?php
/*  код обслуживающий статьи */

function m_articles($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_articles.html", array(), false);
    if(isset($_GET['id']))
	   $id = $_GET['id'];
	else 
	   $id = 80; /* Id статьи по умолчанию, если она не задана через url */

    $article = article_get_by_id($id);
    switch($article) {
    	case EINVAL:
    		$tpl->assign("article_error_message");
    	    break;

    	default:
    		$tpl->assign("article", $article);
    }
    page_set_title($article['page_title']);
    return $tpl->result();
}

?>