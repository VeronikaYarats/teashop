<?php
/*  код обслуживающий статьи */

function m_articles($arg_list)
{
    $tpl = new strontium_tpl("private/tpl/m_articles.html", array(), false);
    $id = $arg_list['id'];
    $key = $arg_list['key'];
    if(isset($arg_list['id']))
        $article = article_get_by_id($id);
    else
        if(isset($arg_list['key']))
            $article = article_get_by_key($key);
        else
            $article = article_get_by_key("welcome");
    
    if( $article < 0 || $article['public'] == 0)
        $tpl->assign("article_error_message");
    else
        $tpl->assign("article", $article);
    page_set_title($article['page_title']);
    return $tpl->result();
}