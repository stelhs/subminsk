<?php


/* Возвращает информацию о пользователе по id */
function user_get_by_id($id)
{
    $query = "SELECT * FROM users WHERE id = " . $id;
    return db()->query($query);
}

/* Возвращает информацию о пользователе если совпадают логин и пароль */
function user_get_by_pass($login, $pass)
{
    $query = "SELECT * FROM users WHERE `login` = '" . $login .
                "' AND `pass` = '" . $pass ."'";
    return db()->query($query);
}

function users_get_list()
{
    $query = "SELECT * FROM users";
    $users = array();
    return db()->query_list($query);
}

function user_change_pass($user_id, $new_login, $new_pass)
{
    if (!is_numeric($user_id) || !isset($user_id) || !$new_pass)
        return EINVAL;

    $query = "UPDATE users " .
                    'SET login = "' . $new_login . '", ' .
                    '`pass` = PASSWORD("' . $new_pass . '") ' .
                    'WHERE id = '. (int)$user_id;

    $result = db()->query($query);

    if ($result == FALSE)
        return ESQL;

    return 0;
}

?>