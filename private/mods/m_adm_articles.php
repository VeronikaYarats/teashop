<?php
/* код обслуживающий режим администрирования статей */

function m_adm_articles($arg_list)
{
    $tpl = new strontium_tpl("private/tpl/m_adm_articles.html",
                            global_conf()['global_marks'], false);

    $mode = 'list_articles';
    if(isset($arg_list['mode']))
        $mode = $arg_list['mode'];

    switch ($mode) {
    /* вывод списка статей */
    case "list_articles":
        page_set_title("статьи");
        $add_url = mk_url(array('mod' => 'adm_articles',
                            'mode' => 'add_article'));
        $tpl->assign("articles_list", array('add_url' => $add_url));
        $articles_list = article_get_list();
        
        
        foreach($articles_list as $article) {
            $url_edit = mk_url(array('mod' => 'adm_articles',
                                    'mode' => 'edit_article',
                                    'id' => $article['id']));
            $article['edit_url'] = $url_edit;
            $url_del = mk_url(array('mod' => 'adm_articles', 
                                    'get_query' => 'del_article', 
                                    'article_id' => $article['id']));
            $article['del_url'] = $url_del;
            $tpl->assign("articles_row_table", $article);
        }
        break;

        /* вывод формы редактирования статьи */
    case "edit_article":
        page_set_title("редактирование статьи");
        $article_id = $arg_list['id'];
        $article = article_get_by_id($article_id);
        if($article['public'] == 1)
            $article['public'] = "checked";

        $tpl->assign("article_add_edit", $article);
        $tpl->assign("article_query_edit");
        $tpl->assign("article_edit", array('id' => $article_id));
        $tpl->assign("article_edit_submit");
        break;

        /* вывод формы добавление статьи */
    case "add_article":
        page_set_title("добавление статьи");
        $tpl->assign("article_add_edit");
        $tpl->assign("article_add");
        $tpl->assign("article_query_add");
        $tpl->assign("article_add_submit");
        break;
    }
    return $tpl->result();
}