<?php
/**
 * Устанавливает title страницы
 * @param title 
 */
$page_title = "";
function page_set_title($title)
{
    global $page_title;
    $page_title = $title;       
}

/**
 * Возвращает title cтраницы
 * 
 */
function page_get_title()
{
    global $page_title;
    return $page_title;
}


?>