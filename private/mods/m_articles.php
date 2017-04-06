<?php
/*  код обслуживающий статьи */

function m_articles($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_articles.html", 
                             global_conf()['global_marks'], false);
    
    if(isset($argv['id']))
        $article = article_get_by_id($argv['id']);
    else 
        if(isset($argv['key']))
            $article = article_get_by_key($argv['key']);
        else 
            $article = article_get_by_key("welcome");
    if($article < 0)
            $tpl->assign("article_error_message");
    else
        $tpl->assign("article", $article);
    page_set_title($article['page_title']);
    return $tpl->result();
}

?>