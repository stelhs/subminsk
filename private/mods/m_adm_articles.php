<?php
/* код обслуживающий режим администрирования статей */

function m_adm_articles($argv = array())
{
	global $global_marks;
	$tpl = new strontium_tpl("private/tpl/m_adm_articles.html",
                             $global_marks, false); 

    $mode = 'list_articles';
	if(isset($argv['mode']))
        $mode = $argv['mode'];

	switch ($mode) {
        /* вывод списка статей */
        case "list_articles":
            $tpl->assign("articles_list");
            $articles_list = article_get_list();
            foreach($articles_list as $article)
                $tpl->assign("articles_row_table", $article);
            break;

        /* вывод формы редактирования статьи */
		case "edit_article":
			$article_id = $argv['id'];
			$article = article_get_by_id($article_id);
			if($article['public'] == 1)
                $article['public'] = "checked";

            $tpl->assign("article_add_edit", $article);
            $tpl->assign("article_query_edit") ;
            $tpl->assign("article_edit", array('id' => $article_id));
            $tpl->assign("article_edit_submit");
			break;

        /* вывод формы добавление статьи */
		case "add_article": 
			$tpl->assign("article_add_edit");
			$tpl->assign("article_add");
			$tpl->assign("article_query_add");
			$tpl->assign("article_add_submit");
			break;
	}
    return $tpl->result();
}
?>