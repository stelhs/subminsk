<?php
/* Функции для реализации авторизации администраторов стайта */


/**
 * Сохранить администратора в сессию
 * @param $adm_id - ID администратора сайта
 */
function auth_store_admin($adm_id)
{
    global $_SESSION;
    $_SESSION['auth_adm_id'] = $adm_id;
}


/**
 * Функция проверяет, авторизован ли администратор.
 * @return - Если администратор авторизован
 *           то возращается id администратора,
 *           в противном случае FALSE
 */
function auth_get_admin()
{
    return isset($_SESSION['auth_adm_id']) ? $_SESSION['auth_adm_id'] : FALSE;
}

/**
 * Удаляет администратора из сессии
 */
function auth_adm_remove()
{
    unset($_SESSION['auth_adm_id']);
}
?>