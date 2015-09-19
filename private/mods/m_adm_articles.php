<?php
//код обслуживающий мод администрирования статей

function m_adm_articles()
{
	global $global_marks;
	$tpl = new strontium_tpl("private/tpl/m_adm_articles.html", $global_marks, false); 
	if(!isset($_GET['mode'])) { // список статей
	    $tpl->assign("articles_list");
        $articles_list = print_articles();
        foreach($articles_list as $article)
            $tpl->assign("articles_row_table",$article);
	}
	else 
	   ($mode = $_GET['mode']);
	switch ($mode) { 
		case "edit_article": // редактирование статьи
			$article_id = $_GET['article_id'];
			$article = article_get_by_id($article_id);
			if($article['public'] == 1)
                $article['public'] = "checked";
            $tpl->assign("edit_article", $article);
			break;
			
		case "add_article": // добавление статьи
			$tpl->assign("add_article");
			break;
	}
    return $tpl->result();
}
?>